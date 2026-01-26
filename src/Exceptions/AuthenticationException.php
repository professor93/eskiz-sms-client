<?php

namespace Uzbek\EskizSmsClient\Exceptions;

class AuthenticationException extends EskizException
{
    public static function invalidCredentials(): self
    {
        return new self('Invalid email or password', 401);
    }

    public static function tokenExpired(): self
    {
        return new self('Token has expired', 401);
    }

    public static function tokenMissing(): self
    {
        return new self('Authentication token is missing', 401);
    }
}
