<?php

namespace Uzbek\EskizSmsClient\Responses;

readonly class SendResponse
{
    public function __construct(
        public string $id,
        public string $status,
        public string $message,
        public ?string $phone = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? $data['data']['id'] ?? ''),
            status: $data['status'] ?? 'unknown',
            message: $data['message'] ?? '',
            phone: $data['phone'] ?? $data['data']['phone'] ?? null,
        );
    }

    public function isOk(): bool
    {
        return $this->status === 'success' || $this->status === 'waiting';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'message' => $this->message,
            'phone' => $this->phone,
        ];
    }
}
