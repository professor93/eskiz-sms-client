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
            Http::macro('eskiz', function () use ($config): PendingRequest {
                $options = [];
                $proxy_url = $config['proxy_url'] ?? (($config['proxy_proto'] ?? '') . '://' . ($config['proxy_host'] ?? '') . ':' . ($config['proxy_port'] ?? '')) ?? '';
                if (is_string($proxy_url) && str_contains($proxy_url, '://') && strlen($proxy_url) > 12) {
                    $options['proxy'] = $proxy_url;
                }

                return Http::baseUrl($config['api_url'])->withOptions($options);
            });

            return new EskizSmsClient(
                email: $config['email'],
                password: $config['password'],
                tokenLifetime: $config['token_lifetime'],
                sender: $config['sender'] ?? '4546',
            );
        });
    }
}
