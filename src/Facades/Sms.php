<?php

namespace Uzbek\EskizSmsClient\Facades;

use Illuminate\Support\Facades\Facade;
use Uzbek\EskizSmsClient\EskizSmsClient;
use Uzbek\EskizSmsClient\Responses\LimitResponse;
use Uzbek\EskizSmsClient\Responses\SendResponse;
use Uzbek\EskizSmsClient\Responses\UserResponse;

/**
 * @method static SendResponse send(string $phone, string $message, ?string $from = null)
 * @method static UserResponse user()
 * @method static LimitResponse limits()
 * @method static string balance()
 * @method static void refreshToken()
 * @method static array about()
 *
 * @see \Uzbek\EskizSmsClient\EskizSmsClient
 */
class Sms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EskizSmsClient::class;
    }
}
