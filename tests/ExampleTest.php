<?php

use Uzbek\EskizSmsClient\Facades\Sms;

it('can resolve the facade', function () {
    expect(Sms::getFacadeRoot())->toBeInstanceOf(\Uzbek\EskizSmsClient\EskizSmsClient::class);
});
