<?php

use Uzbek\EskizSmsClient\Responses\UserResponse;

it('creates from array with direct data', function () {
    $response = UserResponse::fromArray([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'user',
        'status' => 'active',
        'balance' => '1000',
        'is_vip' => true,
    ]);

    expect($response->id)->toBe(1)
        ->and($response->name)->toBe('Test User')
        ->and($response->email)->toBe('test@example.com')
        ->and($response->role)->toBe('user')
        ->and($response->status)->toBe('active')
        ->and($response->balance)->toBe('1000')
        ->and($response->isVip)->toBeTrue();
});

it('creates from array with nested data', function () {
    $response = UserResponse::fromArray([
        'data' => [
            'id' => 2,
            'name' => 'Nested User',
            'email' => 'nested@example.com',
            'role' => 'admin',
            'status' => 'active',
        ],
    ]);

    expect($response->id)->toBe(2)
        ->and($response->name)->toBe('Nested User')
        ->and($response->email)->toBe('nested@example.com')
        ->and($response->role)->toBe('admin');
});

it('returns true for active status', function () {
    $response = UserResponse::fromArray([
        'id' => 1,
        'name' => 'Test',
        'email' => 'test@test.com',
        'role' => 'user',
        'status' => 'active',
    ]);

    expect($response->isActive())->toBeTrue();
});

it('returns false for inactive status', function () {
    $response = UserResponse::fromArray([
        'id' => 1,
        'name' => 'Test',
        'email' => 'test@test.com',
        'role' => 'user',
        'status' => 'inactive',
    ]);

    expect($response->isActive())->toBeFalse();
});

it('converts to array', function () {
    $response = UserResponse::fromArray([
        'id' => 1,
        'name' => 'Test',
        'email' => 'test@test.com',
        'role' => 'user',
        'status' => 'active',
        'balance' => '500',
        'is_vip' => false,
    ]);

    expect($response->toArray())->toBe([
        'id' => 1,
        'name' => 'Test',
        'email' => 'test@test.com',
        'role' => 'user',
        'status' => 'active',
        'balance' => '500',
        'is_vip' => false,
    ]);
});
