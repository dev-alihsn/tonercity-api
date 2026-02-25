<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = 'products/'.fake()->unique()->uuid().'.jpg';

        return [
            'disk' => 'public',
            'path' => $path,
            'type' => fake()->randomElement(['image', 'video']),
            'alt' => fake()->optional(0.5)->sentence(),
        ];
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
        ]);
    }
}
