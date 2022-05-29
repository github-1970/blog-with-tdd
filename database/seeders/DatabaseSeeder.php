<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        User::truncate();
        User::factory(10)->create();
        Post::truncate();
        Post::factory(10)->create();
        Comment::truncate();
        Comment::factory(10)->create();
        DB::table('post_tag')->truncate();
        Tag::truncate();
        Tag::factory(10)->create();

        Schema::enableForeignKeyConstraints();
    }
}
