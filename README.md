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

You can require the package via composer:

```bash
composer require jtd420/laravel-pgp
```

### Installing the package

To install the package into your Laravel application, you need to require it using composer and run the following
Artisan command:

```bash
php artisan PGP::install blade
```

If you prefer a dark theme, the following command can be run instead with the `--dark` option:

```bash
php artisan PGP::install blade --dark
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="PGP-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="PGP-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * To prevent naming conflicts, this prefix will be added to all Laravel-PGP migrations.
     */
    'table_prefix' => 'pgp',

    /*
     * Prefix added to rest of Laravel-PGP code, including routes. (Delete default to remove prefix)
     */
    'prefix' => 'PGP',

    /*
     * Choose the layout file to be extended by the views provided in the package.
     * The default layout file is set to 'layouts.app', but can be changed to match your preferred layout.
     */
    'layout_file' => 'layouts.app',
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="PGP-views"
```

## Usage

This package includes the PGPController class located at `App/Http/Controllers/PGPController` for users who want to
understand the inner workings of the package and possibly modify it. However, if all you want is the default encrypted
messaging functionality, there's no need for any additional setup. The package provides sensible defaults for easy use,
as well as being highly customizable for those who want to extend it.

To use the class and its methods, it must be imported into a controller and instantiated:

```php
use App\Http\Controllers\PGPController;
$controller = new PGPController();
```

### Generating a Keypair

The `generate_keypair` method generates a public/private keypair and returns both the public and private key as
enarmoured PGP keys.

```php
$keypair = $controller->generate_keypair('Name', 'email@email.com', 'RealSecurePassPhrase');
$public_key = $keypair['public_key'];
$private_key = $keypair['private_key'];
```

### Encrypting a Message

The `encrypt` method takes a public key and a message and returns an encrypted enarmoured PGP message.

```php
$encrypted_message = $controller->encrypt($public_key, 'secret message');
```

### Decrypting a Message

The `decrypt` method takes a private key, encrypted message and passphrase and returns either the decrypted message or a
json error if decryption fails.

```php
$decrypted_message = $controller->decrypt($private_key, $encrypted_message, 'RealSecurePassPhrase');
```

For more, see the wiki [here!](https://github.com/JTD420/laravel-pgp/wiki#usage)

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
