<?php

namespace Uzbek\EskizSmsClient\Http\Fakes;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendSms
{
    public function __construct()
    {
        Str::random(10);
        Http::fake(function () {

        });
    }
}
