<?php

namespace Uzbek\EskizSmsClient;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Uzbek\EskizSmsClient\Exceptions\AuthenticationException;
use Uzbek\EskizSmsClient\Exceptions\EskizException;
use Uzbek\EskizSmsClient\Exceptions\RateLimitException;
use Uzbek\EskizSmsClient\Responses\LimitResponse;
use Uzbek\EskizSmsClient\Responses\SendResponse;
use Uzbek\EskizSmsClient\Responses\UserResponse;

class EskizSmsClient
{
    private const CACHE_KEY = 'eskiz_sms_auth_token';

    private ?string $token = null;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly int $tokenLifetime,
        private readonly string $sender
    ) {
        $this->loadToken();
    }

    public function send(string $phone, string $message, ?string $from = null): SendResponse
    {
        $response = $this->request('post', 'message/sms/send', [
            'mobile_phone' => $phone,
            'message' => $message,
            'from' => $from ?? $this->sender,
        ]);

        return SendResponse::fromArray($response);
    }

    public function user(): UserResponse
    {
        $response = $this->request('get', 'auth/user');

        return UserResponse::fromArray($response);
    }

    public function limits(): LimitResponse
    {
        $response = $this->request('get', 'user/get-limit');

        return LimitResponse::fromArray($response);
    }

    public function balance(): string
    {
        return $this->user()->balance ?? '0';
    }

    public function refreshToken(): void
    {
        cache()->forget(self::CACHE_KEY);
        $this->token = null;
        $this->authenticate();
    }

    /**
     * @deprecated Use user() instead. Will be removed in v2.0.
     */
    public function about(): array
    {
        return $this->request('get', 'auth/user');
    }

    private function loadToken(): void
    {
        $this->token = cache()->get(self::CACHE_KEY);

        if ($this->token === null) {
            $this->authenticate();
        }
    }

    private function authenticate(): void
    {
        $response = Http::eskiz()->post('auth/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->status() === 401) {
            throw AuthenticationException::invalidCredentials();
        }

        $this->handleErrorResponse($response);

        $data = $response->json();
        $this->token = $data['data']['token'] ?? null;

        if ($this->token === null) {
            throw AuthenticationException::tokenMissing();
        }

        cache()->put(self::CACHE_KEY, $this->token, $this->tokenLifetime);
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $response = $this->makeRequest($method, $endpoint, $data);

        if ($response->status() === 401) {
            $this->refreshToken();
            $response = $this->makeRequest($method, $endpoint, $data);
        }

        $this->handleErrorResponse($response);

        return $response->json() ?? [];
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $request = Http::eskiz()->withToken($this->token);

        return match ($method) {
            'get' => $request->get($endpoint, $data),
            'post' => $request->post($endpoint, $data),
            'put' => $request->put($endpoint, $data),
            'delete' => $request->delete($endpoint, $data),
            default => throw new EskizException("Unsupported HTTP method: {$method}"),
        };
    }

    private function handleErrorResponse(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $data = $response->json() ?? [];
        $status = $response->status();

        if ($status === 429) {
            $retryAfter = $response->header('Retry-After');

            throw RateLimitException::fromResponse($data, $retryAfter ? (int) $retryAfter : null);
        }

        if ($status === 401) {
            throw AuthenticationException::fromResponse($data, $status);
        }

        throw EskizException::fromResponse($data, $status);
    }
}
