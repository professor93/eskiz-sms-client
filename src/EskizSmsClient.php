<?php

namespace Uzbek\EskizSmsClient;

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
     * @param string $number
     * @param string $text
     * @return array
     */
    public function send(string $number, string $text): array
    {
        return Http::eskiz()->withToken($this->token)->post('message/sms/send', [
            'mobile_phone' => $number,
            'message' => $text,
            'from' => $this->sender,
        ])->json();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function about(): array
    {
        return Http::eskiz()->get('auth/user')->json();
    }

    /**
     * @return array
     */
    public function limits(): array
    {
        return Http::eskiz()->get('user/get-limit')->json();
    }
}
