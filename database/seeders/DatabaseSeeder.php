<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $now = now();
        Category::insert([
            [
                'name' => 'technology',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'business',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'sports',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'entertainment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'world',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
        Source::insert([
            [
                'name' => 'NewsAPI',
                'url' => 'https://newsapi.org/',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
        Article::factory(20)->create();
    }
}
