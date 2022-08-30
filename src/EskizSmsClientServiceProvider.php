<?php

namespace Uzbek\EskizSmsClient;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EskizSmsClientServiceProvider extends PackageServiceProvider
{
    private $config = [];

    public function configurePackage(Package $package): void
    {
        $package->name('eskiz-sms-client')->hasConfigFile();
        $this->app->singleton(EskizSmsClient::class, function () {
            $this->config = config('eskiz-sms-client');
            Http::macro('eskiz', function (): PendingRequest {
                $options = [];
                if ($this->hasConfig('proxy_url') && str_contains($this->config['proxy_url'], '://')) {
                    $options['proxy'] = $this->config['proxy_url'];
                } elseif ($this->hasConfig('proxy_proto', 'proxy_host', 'proxy_port')) {
                    $options['proxy'] = $this->config['proxy_proto'] . '://' . $this->config['proxy_host'] . ':' . $this->config['proxy_port'];
                }

                return Http::baseUrl($this->config['api_url'])->withOptions($options);
            });

            return new EskizSmsClient(
                email: $this->config['email'],
                password: $this->config['password'],
                tokenLifetime: $this->config['token_lifetime'],
                sender: $this->config['sender'] ?? '4546',
            );
        });
    }

    private function hasConfig(...$keys): bool
    {
        foreach ($keys as $key) {
            $val = $this->config[$key] ?? false;
            if ($val === false || (is_string($val) && strlen($val) === 0) || (is_array($val) && count($val) === 0)) {
                return false;
            }
        }

        return true;
    }
}
