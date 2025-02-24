<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitA;

class UsesA extends Model
{
    use TraitA;
}
