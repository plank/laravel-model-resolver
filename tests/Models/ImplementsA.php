<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceA;

class ImplementsA extends Model implements InterfaceA {}
