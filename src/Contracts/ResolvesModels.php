<?php

namespace Plank\LaravelModelResolver\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ResolvesModels
{
    /**
     * Get all Models defined in the Application or Vendor files
     *
     * @return Collection<class-string<Model>>
     */
    public function all(): Collection;

    /**
     * Get the Model that defines the given table name.
     *
     * @return class-string<Model>|null
     */
    public function fromTable(string $table): ?string;

    /**
     * Get all Models that implement the given interfaces
     *
     * @param class-string $interface
     * @return Collection<class-string<Model>>
     */
    public function implements(string $interface): Collection;

    /**
     * Get all Models that implement all the given interfaces
     *
     * @param array<class-string> $interfaces
     * @return Collection<class-string<Model>>
     */
    public function implementsAll(array $interfaces): Collection;

    /**
     * Get all Models that implement at least one of the given interfaces
     *
     * @param array<class-string> $interfaces
     * @return Collection<class-string<Model>>
     */
    public function implementsAny(array $interfaces): Collection;

    /**
     * Get all Models that use the given trait
     *
     * @param class-string $trait
     * @return Collection<class-string<Model>>
     */
    public function uses(string $trait): Collection;

    /**
     * Get all Models that use all the given traits
     *
     * @param array<class-string> $traits
     * @return Collection<class-string<Model>>
     */
    public function usesAll(array $traits): Collection;

    /**
     * Get all Models that use at least one of the given traits
     *
     * @param array<class-string> $traits
     * @return Collection<class-string<Model>>
     */
    public function usesAny(array $traits): Collection;
}
