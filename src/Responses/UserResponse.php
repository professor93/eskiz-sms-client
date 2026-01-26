<?php

namespace Uzbek\EskizSmsClient\Responses;

readonly class UserResponse
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
        public string $status,
        public ?string $balance = null,
        public ?bool $isVip = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $userData = $data['data'] ?? $data;

        return new self(
            id: (int) ($userData['id'] ?? 0),
            name: $userData['name'] ?? '',
            email: $userData['email'] ?? '',
            role: $userData['role'] ?? '',
            status: $userData['status'] ?? '',
            balance: $userData['balance'] ?? null,
            isVip: isset($userData['is_vip']) ? (bool) $userData['is_vip'] : null,
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'balance' => $this->balance,
            'is_vip' => $this->isVip,
        ];
    }
}
