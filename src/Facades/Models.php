<?php

namespace Plank\LaravelModelResolver\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Plank\LaravelModelResolver\Contracts\ResolvesModels;

/**
 * @see \Plank\LaravelModelResolver\Repository\ModelRepository
 *
 * @method static Collection<class-string<Model>> all()
 * @method static class-string<Model>|null fromTable(string $table):
 * @method static Collection<class-string<Model>> implements(class-string $interface)
 * @method static Collection<class-string<Model>> implementsAll(array<class-string> $interfaces)
 * @method static Collection<class-string<Model>> implementsAny(array<class-string> $interfaces)
 * @method static Collection<class-string<Model>> uses(class-string $trait)
 * @method static Collection<class-string<Model>> usesAll(array<class-string> $traits)
 * @method static Collection<class-string<Model>> usesAny(array<class-string> $traits)
 */
class Models extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ResolvesModels::class;
    }
}
