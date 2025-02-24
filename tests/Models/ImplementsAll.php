<?php

namespace Plank\LaravelModelResolver\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceA;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceB;
use Plank\LaravelModelResolver\Tests\Models\Contracts\InterfaceC;

class ImplementsAll extends Model implements InterfaceA, InterfaceB, InterfaceC
{

}