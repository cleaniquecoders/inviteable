
[![Build Status](https://travis-ci.org/cleanique-coders/inviteable.svg?branch=master)](https://travis-ci.org/cleanique-coders/inviteable) [![Latest Stable Version](https://poser.pugx.org/cleanique-coders/inviteable/v/stable)](https://packagist.org/packages/cleanique-coders/inviteable) [![Total Downloads](https://poser.pugx.org/cleanique-coders/inviteable/downloads)](https://packagist.org/packages/cleanique-coders/inviteable) [![License](https://poser.pugx.org/cleanique-coders/inviteable/license)](https://packagist.org/packages/cleanique-coders/inviteable)

## About Your Package

Tell people about your package

## Installation

1. In order to install `cleanique-coders/inviteable` in your Laravel project, just run the *composer require* command from your terminal:

```
$ composer require cleanique-coders/inviteable
```

2. Then in your `config/app.php` add the following to the providers array:

```php
CleaniqueCoders\Inviteable\InviteableServiceProvider::class,
```

3. In the same `config/app.php` add the following to the aliases array:

```php
'Inviteable' => CleaniqueCoders\Inviteable\InviteableFacade::class,
```

## Usage

## Test

To run the test, type `vendor/bin/phpunit` in your terminal.

To have codes coverage, please ensure to install PHP XDebug then run the following command:

```
$ vendor/bin/phpunit -v --coverage-text --colors=never --stderr
```

## Contributions

Everyone are welcome to contribute to this package. However, it's a good practice to provide:

1. The problem you solved
2. Provide test
3. Documentation

Without these 3, you may add extra work for the maintainer.

## License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).