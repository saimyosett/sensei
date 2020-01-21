<?php

use App\Auth\Permissions\PermissionService;
use App\Auth\Role;
use App\Auth\User;
use App\Entities\Bookshelf;
use App\Entities\Chapter;
use App\Entities\Page;
use App\Entities\SearchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an editor user
        $editorUser = factory(User::class)->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        // Create a viewer user
        $viewerUser = factory(User::class)->create();
        $role = Role::getRole('viewer');
        $viewerUser->attachRole($role);

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id];

        factory(\App\Entities\Book::class, 5)->create($byData)
            ->each(function ($book) use ($editorUser, $byData) {
                $chapters = factory(Chapter::class, 3)->create($byData)
                    ->each(function ($chapter) use ($editorUser, $book, $byData) {
                        $pages = factory(Page::class, 3)->make(array_merge($byData, ['book_id' => $book->id]));
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = factory(Page::class, 3)->make($byData);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = factory(\App\Entities\Book::class)->create(array_merge($byData, ['name' => 'Large book' . Str::random(10)]));
        $pages = factory(Page::class, 200)->make($byData);
        $chapters = factory(Chapter::class, 50)->make($byData);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        $shelves = factory(Bookshelf::class, 10)->create($byData);
        $largeBook->shelves()->attach($shelves->pluck('id'));

        app(PermissionService::class)->buildJointPermissions();
        app(SearchService::class)->indexAllEntities();
    }
}
