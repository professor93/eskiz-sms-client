<?php

use Uzbek\EskizSmsClient\Exceptions\AuthenticationException;
use Uzbek\EskizSmsClient\Exceptions\EskizException;
use Uzbek\EskizSmsClient\Exceptions\RateLimitException;

describe('EskizException', function () {
    it('creates from response with message', function () {
        $exception = EskizException::fromResponse([
            'message' => 'Something went wrong',
        ], 500);

        expect($exception->getMessage())->toBe('Something went wrong')
            ->and($exception->getCode())->toBe(500)
            ->and($exception->response)->toBe(['message' => 'Something went wrong']);
    });

    it('creates from response with error', function () {
        $exception = EskizException::fromResponse([
            'error' => 'An error occurred',
        ], 400);

        expect($exception->getMessage())->toBe('An error occurred');
    });

    it('uses default message when none provided', function () {
        $exception = EskizException::fromResponse([], 500);

        expect($exception->getMessage())->toBe('Unknown Eskiz API error');
    });
});

describe('AuthenticationException', function () {
    it('creates invalid credentials exception', function () {
        $exception = AuthenticationException::invalidCredentials();

        expect($exception->getMessage())->toBe('Invalid email or password')
            ->and($exception->getCode())->toBe(401);
    });

    it('creates token expired exception', function () {
        $exception = AuthenticationException::tokenExpired();

        expect($exception->getMessage())->toBe('Token has expired')
            ->and($exception->getCode())->toBe(401);
    });

    it('creates token missing exception', function () {
        $exception = AuthenticationException::tokenMissing();

        expect($exception->getMessage())->toBe('Authentication token is missing')
            ->and($exception->getCode())->toBe(401);
    });
});

describe('RateLimitException', function () {
    it('creates from response', function () {
        $exception = RateLimitException::fromResponse([
            'message' => 'Too many requests',
        ], 60);

        expect($exception->getMessage())->toBe('Too many requests')
            ->and($exception->getCode())->toBe(429)
            ->and($exception->retryAfter)->toBe(60);
    });

    it('uses default message when none provided', function () {
        $exception = RateLimitException::fromResponse([]);

        expect($exception->getMessage())->toBe('Too many requests. Please try again later.');
    });

    it('has nullable retry after', function () {
        $exception = RateLimitException::fromResponse([], null);

        expect($exception->retryAfter)->toBeNull();
    });
});
