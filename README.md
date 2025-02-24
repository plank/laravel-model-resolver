<p align="center"><a href="https://plank.co"><img src="art/model-resolver.png" width="100%"></a></p>

<p align="center">
<a href="https://packagist.org/packages/plank/laravel-model-resolver"><img src="https://img.shields.io/packagist/php-v/plank/laravel-model-resolver?color=%23fae370&label=php&logo=php&logoColor=%23fff" alt="PHP Version Support"></a>
<a href="https://laravel.com/docs/11.x/releases#support-policy"><img src="https://img.shields.io/badge/laravel-10.x,%2011.x-%2343d399?color=%23f1ede9&logo=laravel&logoColor=%23ffffff" alt="PHP Version Support"></a>
<a href="https://github.com/plank/laravel-model-resolver/actions?query=workflow%3Arun-tests"><img src="https://img.shields.io/github/actions/workflow/status/plank/laravel-model-resolver/run-tests.yml?branch=main&&color=%23bfc9bd&label=run-tests&logo=github&logoColor=%23fff" alt="GitHub Workflow Status"></a>
<a href="https://codeclimate.com/github/plank/laravel-model-resolver/test_coverage"><img src="https://img.shields.io/codeclimate/coverage/plank/laravel-model-resolver?color=%23ff9376&label=test%20coverage&logo=code-climate&logoColor=%23fff" /></a>
<a href="https://codeclimate.com/github/plank/laravel-model-resolver/maintainability"><img src="https://img.shields.io/codeclimate/maintainability/plank/laravel-model-resolver?color=%23528cff&label=maintainablility&logo=code-climate&logoColor=%23fff" /></a>
</p>

# Laravel Model Resolver

Resolve all defined Models from the application or dependencies.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Usage](#usage)
  - [all](#all)
  - [fromTable](#fromtable)
  - [implements](#implements)
  - [implementsAll](#implementsall)
  - [implementsAny](#implementsany)
  - [uses](#uses)
  - [usesAll](#usesall)
  - [usesAny](#usesany)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)
- [Security Vulnerabilities](#security-vulnerabilities)
- [About Plank](#check-us-out)

## Installation

You can install the package via composer:

```bash
composer require plank/laravel-model-resolver
```

You can publish the config file with:

```bash
php artisan laravel-model-resolver:install
```

## Quick Start

1. Install the package
2. Run `composer dump-autoload --optimize`

```php
<?php

namespace App\Listeners;

use App\Contracts\Content;
use Plank\LaravelModelResolver\Facades\Models;

class PruneContent
{
    public function handle()
    {
        Models::implements(Content::class)
            ->each(fn (Content $content) => $content->prune());
    }
}
```

## Configuration

The configuration file allows you to customize:

- The repository implementation which resolves the defined Models
- Namespaces to ignore from being scanned for Models

```php
use Plank\LaravelModelResolver\Repository\ModelRepository;

return [
    'repository' => ModelRepository::class,
    'ignore' => [
        'DeepCopy\\',
        'Doctrine\\',
        'Illuminate\\',
        'Mockery\\',
        'PHPStan\\',
        'PHPUnit\\',
        'Prophecy\\',
        'Psr\\',
        'Psy\\',
        'Sebastian\\',
        'Symfony\\',
    ],
];
```

## Usage

It is crucial to note that this package relies on the existence of the file `vendor/composer/autoload_classmap.php` which is created by running `composer dump-autoload --optimize`. If this file does not exist, the package will fail to resolve Models and throw an error.

### all

This method returns the class strings of all Models defined in the application and vendor namespaces.

```php
    public function modelInstances()
    {
        Models::all()
            ->map(function (string $class) => new $class);
    }
```

### fromTable

This method returns a class string of the Model which defines that table name, if one exists.

```php
    public function handle(TableCreated $event)
    {
        $model = Models::fromTable($event->table);

        // ...
    }
```

### implements

This method returns the class strings of Models which implement the given interface.

```php
    public function handle(Version $from, Version $to)
    {
        Models::implements(Versioned::class)
            ->each(fn (string $class) => $class::copyData($from, $to));
    }
```

### implementsAll

This method returns the class strings of the Models which implement all the given interfaces.

```php
    public function handle()
    {
        Models::implementsAll([Loggable::class, Titleable::class])
            ->each(function (string $class) {
                $models = $class::query()
                    ->whereCondition()
                    ->cursor()
                    ->each(fn (Loggable&Titleable $model) => $model->log($model->title().' has met some condition'));
            });
    }
```

### implementsAny

This method returns the class strings of the Models which match any of the given interfaces. This is method should not exist as common interfaces should be extracted, but this method allows shortcuts where appropriate.

```php
    public function handle()
    {
        Models::implementsAny([Expires::class, QueuesForDeletion::class])
            ->each(function (string $class) {
                $models = $class::query()
                    ->when(
                        is_a($class, QueuesForDeletion::class, true),
                        fn ($query) => $query->where('should_delete', true)
                    )
                    ->when(
                        is_a($class, Expires::class, true),
                        fn ($query) => $query->where('expires_at', '>=', now())
                    )
                    ->cursor()
                    ->each(fn (Expires|QueuesForDeletion $model) => $model->delete());
            });
    }
```

### uses

This method returns the class strings of all Models that use the given trait.

```php
    public function purge()
    {
        Models::uses(SoftDeletes::class)
            ->each(function (string $class) {
                $models = $class::query()
                    ->whereNotNull('deleted_at')
                    ->cursor()
                    ->each(fn (Model $model) => $model->forceDelete());
            });
    }
```

### usesAll

This method returns the class strings of Models that use all the given traits.

```php
    public function handle()
    {
        Models::usesAll([GetsLogged::class, HasTitle::class])
            ->each(function (string $class) {
                $models = $class::query()
                    ->whereCondition()
                    ->cursor()
                    ->each(fn (Model $model) => $model->log($model->title().' has met some condition'));
            });
    }
```

### usesAny

This method returns the class strings of Models that use all the given traits.

```php
    public function handle()
    {
        Models::implementsAny([GetsExpired::class, IsQueuedForDeletion::class])
            ->each(function (string $class) {
                $uses = class_uses_recursive($class);

                $models = $class::query()
                    ->when(
                        in_array(GetsExpired::class, $uses),
                        fn ($query) => $query->where('should_delete', true)
                    )
                    ->when(
                        in_array(IsQueuedForDeletion::class, $uses),
                        fn ($query) => $query->where('expires_at', '>=', now())
                    )
                    ->cursor()
                    ->each(fn (Model $model) => $model->delete());
            });
    }
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

&nbsp;

## Credits

- [Kurt Friars](https://github.com/kfriars)
- [All Contributors](../../contributors)

&nbsp;

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

&nbsp;

## Security Vulnerabilities

If you discover a security vulnerability within siren, please send an e-mail to [security@plank.co](mailto:security@plank.co). All security vulnerabilities will be promptly addressed.

&nbsp;

## Check Us Out!

<a href="https://plank.co/open-source/learn-more-image">
    <img src="https://plank.co/open-source/banner">
</a>

&nbsp;

Plank focuses on impactful solutions that deliver engaging experiences to our clients and their users. We're committed to innovation, inclusivity, and sustainability in the digital space. [Learn more](https://plank.co/open-source/learn-more-link) about our mission to improve the web.
