<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitA;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitB;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitC;

class UsesAll extends Model
{
    use TraitA;
    use TraitB;
    use TraitC;
}
