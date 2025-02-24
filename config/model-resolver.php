<?php

use Plank\LaravelModelResolver\Repository\ModelRepository;

return [
    'repository' => ModelRepository::class,
    'ignore' => [
        'DeepCopy\\',
        'Doctrine\\',
        'Illuminate\\',
        'Mockery\\',
        'PHPStan\\',
        'PHPUnit\\',
        'Prophecy\\',
        'Psr\\',
        'Psy\\',
        'Sebastian\\',
        'Symfony\\',
    ],
];
