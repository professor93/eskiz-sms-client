<?php

namespace Uzbek\EskizSmsClient\Exceptions;

use Exception;

class EskizException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        public readonly ?array $response = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(array $response, int $statusCode = 0): self
    {
        $message = $response['message'] ?? $response['error'] ?? 'Unknown Eskiz API error';

        return new self($message, $statusCode, $response);
    }
}
