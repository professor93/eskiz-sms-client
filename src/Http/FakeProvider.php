<?php

namespace Uzbek\EskizSmsClient\Http;

use Uzbek\EskizSmsClient\Http\Fakes\SendSms;

class FakeProvider
{
    private static array $fakes = [
        SendSms::class,
    ];

    public static function register(): void
    {
        foreach (self::$fakes as $fake) {
            new $fake();
        }
    }
}
