<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'post' => $faker->realText($maxNbChars = 200, $indexSize = 2),
    ];
});
