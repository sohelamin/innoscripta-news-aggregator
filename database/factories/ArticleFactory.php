<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
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
            'source_id' => function () {
                return Source::query()->inRandomOrder()->value('id')
                    ?? Source::factory()->create()->id;
            },
            'author_id' => Author::factory(),
            'category_id' => function () {
                return Category::query()->inRandomOrder()->value('id')
                    ?? Category::factory()->create()->id;
            },
        ];
    }
}
