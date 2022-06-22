<?php

namespace Uzbek\EskizSmsClient\Facades;

use Illuminate\Support\Facades\Facade;
use Uzbek\EskizSmsClient\EskizSmsClient;

/**
 * @see \Uzbek\EskizSmsClient\EskizSmsClient
 */
class Sms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EskizSmsClient::class;
    }
}
