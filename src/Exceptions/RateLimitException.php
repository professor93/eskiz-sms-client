<?php

namespace Uzbek\EskizSmsClient\Exceptions;

class RateLimitException extends EskizException
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        public readonly ?int $retryAfter = null
    ) {
        parent::__construct($message, 429);
    }

    public static function fromResponse(array $response, ?int $retryAfter = null): self
    {
        $message = $response['message'] ?? 'Too many requests. Please try again later.';

        return new self($message, $retryAfter);
    }
}
