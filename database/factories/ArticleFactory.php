<?php

$factory->define(App\Article::class, function (Faker\Generator $faker) {
    $tags = collect(['php', 'ruby', 'java', 'javascript', 'bash'])
        ->random(2)
        ->values()
        ->all();

    return [
        'title' => $faker->sentence,
        'body' => $faker->text,
        'tags' => $tags,
    ];
});
