
[![Build Status](https://travis-ci.org/cleaniquecoders/inviteable.svg?branch=master)](https://travis-ci.org/cleaniquecoders/inviteable) [![Latest Stable Version](https://poser.pugx.org/cleaniquecoders/inviteable/v/stable)](https://packagist.org/packages/cleaniquecoders/inviteable) [![Total Downloads](https://poser.pugx.org/cleaniquecoders/inviteable/downloads)](https://packagist.org/packages/cleaniquecoders/inviteable) [![License](https://poser.pugx.org/cleaniquecoders/inviteable/license)](https://packagist.org/packages/cleaniquecoders/inviteable)

## About Your Package

Inviteable, inspired from [Laravel Auth Invitations](https://github.com/LaravelDaily/Laravel-Auth-Invitations), but in this package, not for Auth, but for anything. Yes, we mean anything! Invitation to group, to class room, to meeting. Can be anything!  

## Installation

1. In order to install `cleaniquecoders/inviteable` in your Laravel project, just run the *composer require* command from your terminal:

```
$ composer require cleaniquecoders/inviteable
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