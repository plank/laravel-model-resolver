<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Concerns\TraitB;

class UsesB extends Model
{
    use TraitB;
}