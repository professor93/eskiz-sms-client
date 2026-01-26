# Eskiz.uz sms client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uzbek/eskiz-sms-client.svg?style=flat-square)](https://packagist.org/packages/uzbek/eskiz-sms-client)
[![Total Downloads](https://img.shields.io/packagist/dt/uzbek/eskiz-sms-client.svg?style=flat-square)](https://packagist.org/packages/uzbek/eskiz-sms-client)

Eskiz.uz sms client for Laravel.

## Requirements

- PHP 8.3+
- Laravel 11.x or 12.x

## Installation

You can install the package via composer:

```bash
composer require uzbek/eskiz-sms-client
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="eskiz-sms-client-config"
```

Add the following environment variables to your `.env` file:

```env
ESKIZ_SMS_EMAIL=your-email@example.com
ESKIZ_SMS_PASSWORD=your-password
ESKIZ_SMS_FROM=4546
```

## Usage

### Sending SMS

```php
use Uzbek\EskizSmsClient\Facades\Sms;

// Basic usage
$response = Sms::send('998901234567', 'Hello from Laravel!');

if ($response->isOk()) {
    echo "SMS sent! ID: " . $response->id;
}

// With custom sender
$response = Sms::send('998901234567', 'Hello!', 'CustomSender');
```

### Getting User Info

```php
$user = Sms::user();

echo $user->name;      // User name
echo $user->email;     // User email
echo $user->balance;   // Account balance
echo $user->isActive(); // true/false
```

### Checking Limits

```php
$limits = Sms::limits();

echo $limits->limit;           // Total limit
echo $limits->used;            // Used count
echo $limits->remaining;       // Remaining count
echo $limits->usagePercentage(); // Usage percentage (e.g., 25.5)
echo $limits->hasRemaining();  // true/false
```

### Getting Balance

```php
$balance = Sms::balance();
```

### Refreshing Token

```php
Sms::refreshToken();
```

## Exception Handling

The package throws specific exceptions for different error scenarios:

```php
use Uzbek\EskizSmsClient\Exceptions\AuthenticationException;
use Uzbek\EskizSmsClient\Exceptions\RateLimitException;
use Uzbek\EskizSmsClient\Exceptions\EskizException;

try {
    $response = Sms::send('998901234567', 'Hello!');
} catch (AuthenticationException $e) {
    // Invalid credentials or token issues
} catch (RateLimitException $e) {
    // Too many requests
    $retryAfter = $e->retryAfter; // Seconds to wait
} catch (EskizException $e) {
    // Other API errors
    $response = $e->response; // Raw API response
}
```

## Response Objects

All methods return typed response objects:

- `SendResponse` - SMS send result with `id`, `status`, `message`, `phone`
- `UserResponse` - User info with `id`, `name`, `email`, `role`, `status`, `balance`, `isVip`
- `LimitResponse` - Limits info with `limit`, `used`, `remaining`, `resetAt`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Inoyatulloh](https://github.com/professor93)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
