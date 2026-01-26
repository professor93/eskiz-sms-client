<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Uzbek\EskizSmsClient\EskizSmsClient;
use Uzbek\EskizSmsClient\Exceptions\AuthenticationException;
use Uzbek\EskizSmsClient\Exceptions\EskizException;
use Uzbek\EskizSmsClient\Exceptions\RateLimitException;
use Uzbek\EskizSmsClient\Responses\LimitResponse;
use Uzbek\EskizSmsClient\Responses\SendResponse;
use Uzbek\EskizSmsClient\Responses\UserResponse;

beforeEach(function () {
    Cache::flush();
    Http::preventStrayRequests();
});

it('authenticates and caches token on construction', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token-123'],
        ]),
    ]);

    new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    expect(Cache::get('eskiz_sms_auth_token'))->toBe('test-token-123');
});

it('throws exception for invalid credentials', function () {
    Http::fake([
        '*/auth/login' => Http::response(['message' => 'Invalid credentials'], 401),
    ]);

    new EskizSmsClient(
        email: 'wrong@example.com',
        password: 'wrong',
        tokenLifetime: 3600,
        sender: '4546'
    );
})->throws(AuthenticationException::class, 'Invalid email or password');

it('sends SMS and returns SendResponse', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/message/sms/send' => Http::response([
            'id' => '12345',
            'status' => 'success',
            'message' => 'SMS sent',
        ]),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $response = $client->send('998901234567', 'Hello World');

    expect($response)->toBeInstanceOf(SendResponse::class)
        ->and($response->id)->toBe('12345')
        ->and($response->status)->toBe('success')
        ->and($response->isOk())->toBeTrue();
});

it('gets user info and returns UserResponse', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/auth/user' => Http::response([
            'data' => [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'user',
                'status' => 'active',
                'balance' => '5000',
            ],
        ]),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $response = $client->user();

    expect($response)->toBeInstanceOf(UserResponse::class)
        ->and($response->name)->toBe('Test User')
        ->and($response->balance)->toBe('5000')
        ->and($response->isActive())->toBeTrue();
});

it('gets limits and returns LimitResponse', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/user/get-limit' => Http::response([
            'data' => [
                'limit' => 1000,
                'used' => 100,
            ],
        ]),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $response = $client->limits();

    expect($response)->toBeInstanceOf(LimitResponse::class)
        ->and($response->limit)->toBe(1000)
        ->and($response->used)->toBe(100)
        ->and($response->remaining)->toBe(900)
        ->and($response->hasRemaining())->toBeTrue();
});

it('auto refreshes token on 401 response', function () {
    $callCount = 0;

    Http::fake(function ($request) use (&$callCount) {
        if (str_contains($request->url(), 'auth/login')) {
            $callCount++;

            return Http::response([
                'data' => ['token' => 'new-token-' . $callCount],
            ]);
        }

        if (str_contains($request->url(), 'auth/user')) {
            if ($callCount === 1) {
                return Http::response(['message' => 'Token expired'], 401);
            }

            return Http::response([
                'data' => [
                    'id' => 1,
                    'name' => 'Test',
                    'email' => 'test@test.com',
                    'role' => 'user',
                    'status' => 'active',
                ],
            ]);
        }

        return Http::response([], 404);
    });

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $response = $client->user();

    expect($callCount)->toBe(2)
        ->and($response)->toBeInstanceOf(UserResponse::class);
});

it('throws RateLimitException on 429 response', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/message/sms/send' => Http::response([
            'message' => 'Rate limit exceeded',
        ], 429),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $client->send('998901234567', 'Test');
})->throws(RateLimitException::class);

it('throws EskizException on other errors', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/message/sms/send' => Http::response([
            'message' => 'Internal server error',
        ], 500),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $client->send('998901234567', 'Test');
})->throws(EskizException::class, 'Internal server error');

it('uses custom sender when provided', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/message/sms/send' => function ($request) {
            expect($request->data()['from'])->toBe('CustomSender');

            return Http::response([
                'id' => '1',
                'status' => 'success',
                'message' => 'OK',
            ]);
        },
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    $client->send('998901234567', 'Test', 'CustomSender');
});

it('gets balance from user info', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'data' => ['token' => 'test-token'],
        ]),
        '*/auth/user' => Http::response([
            'data' => [
                'id' => 1,
                'name' => 'Test',
                'email' => 'test@test.com',
                'role' => 'user',
                'status' => 'active',
                'balance' => '12500',
            ],
        ]),
    ]);

    $client = new EskizSmsClient(
        email: 'test@example.com',
        password: 'password',
        tokenLifetime: 3600,
        sender: '4546'
    );

    expect($client->balance())->toBe('12500');
});
