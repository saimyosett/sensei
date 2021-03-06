<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(\App\Auth\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => Str::random(10),
        'remember_token' => Str::random(10),
        'email_confirmed' => 1
    ];
});

$factory->define(\App\Entities\Bookshelf::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'slug' => Str::random(10),
        'description' => $faker->paragraph
    ];
});

$factory->define(\App\Entities\Book::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'slug' => Str::random(10),
        'description' => $faker->paragraph
    ];
});

$factory->define(\App\Entities\Chapter::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'slug' => Str::random(10),
        'description' => $faker->paragraph
    ];
});

$factory->define(\App\Entities\Page::class, function ($faker) {
    $html = '<p>' . implode('</p>', $faker->paragraphs(5)) . '</p>';
    return [
        'name' => $faker->sentence,
        'slug' => Str::random(10),
        'html' => $html,
        'text' => strip_tags($html),
        'revision_count' => 1
    ];
});

$factory->define(\App\Auth\Role::class, function ($faker) {
    return [
        'display_name' => $faker->sentence(3),
        'description' => $faker->sentence(10)
    ];
});

$factory->define(\App\Actions\Tag::class, function ($faker) {
    return [
        'name' => $faker->city,
        'value' => $faker->sentence(3)
    ];
});

$factory->define(\App\Uploads\Image::class, function ($faker) {
    return [
        'name' => $faker->slug . '.jpg',
        'url' => $faker->url,
        'path' => $faker->url,
        'type' => 'gallery',
        'uploaded_to' => 0
    ];
});

$factory->define(\App\Actions\Comment::class, function ($faker) {
    $text = $faker->paragraph(1);
    $html = '<p>' . $text. '</p>';
    return [
        'html' => $html,
        'text' => $text,
        'parent_id' => null
    ];
});
