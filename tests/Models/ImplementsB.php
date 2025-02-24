<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceB;

class ImplementsB extends Model implements InterfaceB
{

}