<?php

use Plank\LaravelModelResolver\Facades\Models;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitA;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitB;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitC;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceA;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceB;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceC;
use Plank\LaravelModelResolver\Tests\Models\ImplementsA;
use Plank\LaravelModelResolver\Tests\Models\ImplementsAll;
use Plank\LaravelModelResolver\Tests\Models\ImplementsB;
use Plank\LaravelModelResolver\Tests\Models\ImplementsNone;
use Plank\LaravelModelResolver\Tests\Models\UsesA;
use Plank\LaravelModelResolver\Tests\Models\UsesAll;
use Plank\LaravelModelResolver\Tests\Models\UsesB;
use Plank\LaravelModelResolver\Tests\Models\UsesNone;

it('resolves all models', function () {
    expect(Models::all())
        ->toContain(ImplementsAll::class)
        ->toContain(ImplementsA::class)
        ->toContain(ImplementsB::class)
        ->toContain(ImplementsNone::class)
        ->toContain(UsesAll::class)
        ->toContain(UsesA::class)
        ->toContain(UsesB::class)
        ->toContain(UsesNone::class);
});

it('resolves models from their table', function () {
    expect(Models::fromTable('implements_alls'))
        ->toBe(ImplementsAll::class);
});

it('resolves implements correctly', function () {
    expect(Models::implements(InterfaceA::class))
        ->toContain(ImplementsAll::class)
        ->toContain(ImplementsA::class)
        ->not->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->not->toContain(UsesAll::class)
        ->not->toContain(UsesA::class)
        ->not->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});

it('resolves implements all correctly', function () {
    expect(Models::implementsAll([InterfaceA::class, InterfaceB::class, InterfaceC::class]))
        ->toContain(ImplementsAll::class)
        ->not->toContain(ImplementsA::class)
        ->not->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->not->toContain(UsesAll::class)
        ->not->toContain(UsesA::class)
        ->not->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});

it('resolves implements any correctly', function () {
    expect(Models::implementsAny([InterfaceA::class, InterfaceB::class]))
        ->toContain(ImplementsAll::class)
        ->toContain(ImplementsA::class)
        ->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->not->toContain(UsesAll::class)
        ->not->toContain(UsesA::class)
        ->not->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});

it('resolves uses correctly', function () {
    expect(Models::uses(TraitA::class))
        ->not->toContain(ImplementsAll::class)
        ->not->toContain(ImplementsA::class)
        ->not->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->toContain(UsesAll::class)
        ->toContain(UsesA::class)
        ->not->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});

it('resolves uses all correctly', function () {
    expect(Models::usesAll([TraitA::class, TraitB::class, TraitC::class]))
        ->not->toContain(ImplementsAll::class)
        ->not->toContain(ImplementsA::class)
        ->not->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->toContain(UsesAll::class)
        ->not->toContain(UsesA::class)
        ->not->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});

it('resolves uses any correctly', function () {
    expect(Models::usesAny([TraitA::class, TraitB::class]))
        ->not->toContain(ImplementsAll::class)
        ->not->toContain(ImplementsA::class)
        ->not->toContain(ImplementsB::class)
        ->not->toContain(ImplementsNone::class)
        ->toContain(UsesAll::class)
        ->toContain(UsesA::class)
        ->toContain(UsesB::class)
        ->not->toContain(UsesNone::class);
});
