<?php

namespace Plank\LaravelModelResolver\Exceptions;

use Exception;

class ModelResolverException extends Exception
{
    public static function missingAutoloader(): self
    {
        return new self('Cannot resolve Models without the autoloading file built. Run `composer dump autoload --optimize` to generate it.');
    }

    public static function emptyMap(): self
    {
        return new self('The model map is empty. Run `composer dump autoload --optimize` to generate the class map.');
    }
}
