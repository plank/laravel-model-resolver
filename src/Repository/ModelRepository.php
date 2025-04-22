<?php

namespace Plank\LaravelModelResolver\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Plank\LaravelModelResolver\Contracts\ResolvesModels;
use Plank\LaravelModelResolver\Contracts\VersionKey;
use Plank\LaravelModelResolver\Exceptions\ModelResolverException;
use ReflectionClass;
use Throwable;

class ModelRepository implements ResolvesModels
{
    /**
     * @var array<string,class-string<Model>> A map of tables to their model class
     */
    protected array $map;

    public function __construct()
    {
        if (! ($autoload = realpath('vendor/composer/autoload_classmap.php'))) {
            throw ModelResolverException::missingAutoloader();
        }

        $this->map = Collection::make(require $autoload)
            ->keys()
            ->reject(fn (string $class) => static::shouldSkipClass($class))
            ->map(function ($class) {
                try {
                    // First check if class exists without autoloading
                    if (! class_exists($class)) {
                        return null;
                    }

                    // Now check if it's a valid model
                    if (! static::isValidModel($class)) {
                        return null;
                    }

                    return static::getTableMapping($class);
                } catch (Throwable) {
                    return null;
                }
            })
            ->filter()
            ->reduce(function (Collection $mapped, array $entry) {
                [$table, $class] = $entry;

                // In many applications consuming applications will override vendor
                // models in the App namespace, and we want to ensure the App's model
                // is resolved for the table.
                if ($mapped->has($table) && str($class)->startsWith('App\\')) {
                    return $mapped;
                }

                $mapped->put($table, $class);

                return $mapped;
            }, new Collection)
            ->all();

        if (empty($this->map)) {
            throw ModelResolverException::emptyMap();
        }
    }

    /**
     * Get all Models defined in the Application or Vendor files
     *
     * @return Collection<class-string<Model>>
     */
    public function all(): Collection
    {
        return new Collection($this->map);
    }

    /**
     * Get the Model that defines the given table name.
     *
     * @return class-string<Model>|null
     */
    public function fromTable(string $table): ?string
    {
        return $this->map[$table] ?? null;
    }

    /**
     * Get all Models that implement the given interfaces
     *
     * @param  class-string  $interface
     * @return Collection<class-string<Model>>
     */
    public function implements(string $interface): Collection
    {
        return (new Collection($this->map))
            ->filter(fn (string $model) => is_a($model, $interface, true));
    }

    /**
     * Get all Models that implement all the given interfaces
     *
     * @param  array<class-string>  $interfaces
     * @return Collection<class-string<Model>>
     */
    public function implementsAll(array $interfaces): Collection
    {
        return (new Collection($this->map))
            ->filter(function (string $model) use ($interfaces) {
                foreach ($interfaces as $interface) {
                    if (! is_a($model, $interface, true)) {
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * Get all Models that implement at least one of the given interfaces
     *
     * @param  array<class-string>  $interfaces
     * @return Collection<class-string<Model>>
     */
    public function implementsAny(array $interfaces): Collection
    {
        return (new Collection($this->map))
            ->filter(function (string $model) use ($interfaces) {
                foreach ($interfaces as $interface) {
                    if (is_a($model, $interface, true)) {
                        return true;
                    }
                }

                return false;
            });
    }

    /**
     * Get all Models that use the given trait
     *
     * @param  class-string  $trait
     * @return Collection<class-string<Model>>
     */
    public function uses(string $trait): Collection
    {
        return (new Collection($this->map))
            ->filter(fn (string $model) => in_array($trait, class_uses_recursive($model)));
    }

    /**
     * Get all Models that use all the given traits
     *
     * @param  array<class-string>  $traits
     * @return Collection<class-string<Model>>
     */
    public function usesAll(array $traits): Collection
    {
        return (new Collection($this->map))
            ->filter(function (string $model) use ($traits) {
                $uses = class_uses_recursive($model);

                foreach ($traits as $trait) {
                    if (! in_array($trait, $uses)) {
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * Get all Models that use at least one of the given traits
     *
     * @param  array<class-string>  $traits
     * @return Collection<class-string<Model>>
     */
    public function usesAny(array $traits): Collection
    {
        return (new Collection($this->map))
            ->filter(function (string $model) use ($traits) {
                $uses = class_uses_recursive($model);

                foreach ($traits as $trait) {
                    if (in_array($trait, $uses)) {
                        return true;
                    }
                }

                return false;
            });
    }

    /**
     * Check if a class should be skipped
     */
    protected static function shouldSkipClass(string $class): bool
    {
        return str($class)->startsWith(config()->get('model-resolver.ignore'));
    }

    /**
     * Check if class is a valid non-abstract model
     */
    protected static function isValidModel(string $class): bool
    {
        try {
            if (! is_a($class, Model::class, true)) {
                return false;
            }

            $reflection = new ReflectionClass($class);

            // Skip traits, interfaces and abstract classes
            if ($reflection->isTrait() || $reflection->isInterface() || $reflection->isAbstract()) {
                return false;
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Get table mapping for a valid model class
     *
     * @param  class-string  $class
     * @param  class-string<VersionKey>  $keyClass
     * @return array<string, class-string|null>
     */
    protected static function getTableMapping(string $class): ?array
    {
        try {
            $model = new $class;

            return [
                $model->getTable(),
                $class,
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
