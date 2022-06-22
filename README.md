# Eskiz.uz sms client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uzbek/eskiz-sms-client.svg?style=flat-square)](https://packagist.org/packages/uzbek/eskiz-sms-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/uzbek/eskiz-sms-client/run-tests?label=tests)](https://github.com/uzbek/eskiz-sms-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/uzbek/eskiz-sms-client/Check%20&%20fix%20styling?label=code%20style)](https://github.com/uzbek/eskiz-sms-client/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/uzbek/eskiz-sms-client.svg?style=flat-square)](https://packagist.org/packages/uzbek/eskiz-sms-client)

Eskiz.uz sms client.

## Installation

You can install the package via composer:

```bash
composer require uzbek/eskiz-sms-client
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="eskiz-sms-client-config"
```

## Usage

```php
Sms::send('998901234567', 'Sms from PHP/Laravel application');
```

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
