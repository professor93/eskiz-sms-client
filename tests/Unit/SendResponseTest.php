<?php

use Uzbek\EskizSmsClient\Responses\SendResponse;

it('creates from array with direct data', function () {
    $response = SendResponse::fromArray([
        'id' => '12345',
        'status' => 'success',
        'message' => 'SMS sent successfully',
        'phone' => '998901234567',
    ]);

    expect($response->id)->toBe('12345')
        ->and($response->status)->toBe('success')
        ->and($response->message)->toBe('SMS sent successfully')
        ->and($response->phone)->toBe('998901234567');
});

it('creates from array with nested data', function () {
    $response = SendResponse::fromArray([
        'status' => 'success',
        'message' => 'SMS queued',
        'data' => [
            'id' => '67890',
            'phone' => '998907654321',
        ],
    ]);

    expect($response->id)->toBe('67890')
        ->and($response->phone)->toBe('998907654321');
});

it('returns true for success status', function () {
    $response = SendResponse::fromArray([
        'id' => '1',
        'status' => 'success',
        'message' => 'OK',
    ]);

    expect($response->isOk())->toBeTrue();
});

it('returns true for waiting status', function () {
    $response = SendResponse::fromArray([
        'id' => '1',
        'status' => 'waiting',
        'message' => 'Queued',
    ]);

    expect($response->isOk())->toBeTrue();
});

it('returns false for failed status', function () {
    $response = SendResponse::fromArray([
        'id' => '1',
        'status' => 'failed',
        'message' => 'Error',
    ]);

    expect($response->isOk())->toBeFalse();
});

it('converts to array', function () {
    $response = SendResponse::fromArray([
        'id' => '123',
        'status' => 'success',
        'message' => 'OK',
        'phone' => '998901234567',
    ]);

    expect($response->toArray())->toBe([
        'id' => '123',
        'status' => 'success',
        'message' => 'OK',
        'phone' => '998901234567',
    ]);
});
