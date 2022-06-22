<?php

namespace Uzbek\EskizSmsClient;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class EskizSmsClient
{
    private string $token;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly int    $tokenLifetime,
        private readonly string $sender
    ) {
        $this->login();
    }

    /**
     * @return void
     */
    private function login(): void
    {
        $this->token = cache()->remember(
            'sms_auth_token',
            $this->tokenLifetime,
            fn () => Http::eskiz()->post('auth/login', ['email' => $this->email, 'password' => $this->password])->object()->data->token
        );
    }

    /**
     * @throws \Exception
     */
    public function send(string $number, string $text): Response
    {
        return Http::eskiz()->withToken($this->token)->post('message/sms/send', [
            'mobile_phone' => $number,
            'message' => $text,
            'from' => $this->sender,
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function about(): array
    {
        return Http::eskiz()->get('auth/user');
    }

    /**
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    public function limits(): Response
    {
        return Http::eskiz()->get('user/get-limit');
    }
}
