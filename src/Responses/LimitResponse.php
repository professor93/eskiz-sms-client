<?php

namespace Uzbek\EskizSmsClient\Responses;

readonly class LimitResponse
{
    public function __construct(
        public int $limit,
        public int $used,
        public int $remaining,
        public ?string $resetAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $limitData = $data['data'] ?? $data;

        $limit = (int) ($limitData['limit'] ?? 0);
        $used = (int) ($limitData['used'] ?? $limitData['count'] ?? 0);

        return new self(
            limit: $limit,
            used: $used,
            remaining: $limit - $used,
            resetAt: $limitData['reset_at'] ?? $limitData['reset'] ?? null,
        );
    }

    public function hasRemaining(): bool
    {
        return $this->remaining > 0;
    }

    public function usagePercentage(): float
    {
        if ($this->limit === 0) {
            return 0.0;
        }

        return round(($this->used / $this->limit) * 100, 2);
    }

    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'used' => $this->used,
            'remaining' => $this->remaining,
            'reset_at' => $this->resetAt,
        ];
    }
}
