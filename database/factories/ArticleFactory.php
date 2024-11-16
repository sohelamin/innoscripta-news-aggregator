<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence,
            'content' => fake()->paragraphs(3, true),
            'url' => fake()->url,
            'image_url' => fake()->imageUrl,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'source_id' => Source::factory(),
            'author_id' => Author::factory(),
        ];
    }
}
