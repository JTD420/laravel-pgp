# Automatic PGP Encryption/Decryption for your laravel application. Fully customisable and works out-the-box with the default provided scaffolding!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jtd420/laravel-pgp.svg?style=flat-square)](https://packagist.org/packages/jtd420/laravel-pgp)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jtd420/laravel-pgp/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jtd420/laravel-pgp/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jtd420/laravel-pgp/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jtd420/laravel-pgp/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jtd420/laravel-pgp.svg?style=flat-square)](https://packagist.org/packages/jtd420/laravel-pgp)

## Important Notice
Please be advised that Laravel-PGP is currently in its early stages of development (version 0.0.1) and may not be suitable for production use in critical environments.

This package provides a convenient way for Laravel users to add PGP encryption and decryption functionality to their projects, with the added bonus of default scaffolding for a chat interface.

However, as with any new software, it is still undergoing testing and refinement. We recommend that users exercise caution and carefully evaluate its suitability for their specific use case before implementing it in a live environment.

## Support us

Future updates will include ways to support this project and others.

[<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" width="419px" />](https://github.com/JTD420/laravel-pgp)



## Installation

You can install the package via composer:

```bash
composer require jtd420/laravel-pgp
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-pgp-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-pgp-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-pgp-views"
```

## Usage

```php
$PGP = new JTD420\PGP();
echo $PGP->echoPhrase('Hello, JTD420!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Brad](https://github.com/JTD420)
- [All Contributors](../../contributors)
- [SingPolyma/OpenPGP-PHP](https://github.com/singpolyma/openpgp-php/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
