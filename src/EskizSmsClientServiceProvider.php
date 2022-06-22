<?php

namespace Uzbek\EskizSmsClient;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EskizSmsClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('eskiz-sms-client')->hasConfigFile();
        $this->app->singleton(EskizSmsClient::class, function () {
            $config = config('eskiz-sms-client');
            Http::macro('eskiz', fn (): PendingRequest => Http::baseUrl($config['api_url']));

            return new EskizSmsClient(
                email: $config['email'],
                password: $config['password'],
                tokenLifetime: $config['token_lifetime'],
                sender: $config['sender'] ?? '4546',
            );
        });
    }
}
