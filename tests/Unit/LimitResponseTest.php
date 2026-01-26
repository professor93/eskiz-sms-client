<?php

use Uzbek\EskizSmsClient\Responses\LimitResponse;

it('creates from array with direct data', function () {
    $response = LimitResponse::fromArray([
        'limit' => 1000,
        'used' => 250,
    ]);

    expect($response->limit)->toBe(1000)
        ->and($response->used)->toBe(250)
        ->and($response->remaining)->toBe(750);
});

it('creates from array with nested data', function () {
    $response = LimitResponse::fromArray([
        'data' => [
            'limit' => 500,
            'count' => 100,
            'reset_at' => '2026-02-01',
        ],
    ]);

    expect($response->limit)->toBe(500)
        ->and($response->used)->toBe(100)
        ->and($response->remaining)->toBe(400)
        ->and($response->resetAt)->toBe('2026-02-01');
});

it('returns true when has remaining', function () {
    $response = LimitResponse::fromArray([
        'limit' => 100,
        'used' => 50,
    ]);

    expect($response->hasRemaining())->toBeTrue();
});

it('returns false when no remaining', function () {
    $response = LimitResponse::fromArray([
        'limit' => 100,
        'used' => 100,
    ]);

    expect($response->hasRemaining())->toBeFalse();
});

it('calculates usage percentage correctly', function () {
    $response = LimitResponse::fromArray([
        'limit' => 1000,
        'used' => 250,
    ]);

    expect($response->usagePercentage())->toBe(25.0);
});

it('returns zero percentage when limit is zero', function () {
    $response = LimitResponse::fromArray([
        'limit' => 0,
        'used' => 0,
    ]);

    expect($response->usagePercentage())->toBe(0.0);
});

it('converts to array', function () {
    $response = LimitResponse::fromArray([
        'limit' => 1000,
        'used' => 300,
        'reset_at' => '2026-01-31',
    ]);

    expect($response->toArray())->toBe([
        'limit' => 1000,
        'used' => 300,
        'remaining' => 700,
        'reset_at' => '2026-01-31',
    ]);
});
