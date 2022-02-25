<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {

        return [
            'content' => $this->faker->realText(500),
            'user_id' =>random_int(1, 9),
            'image_id' =>random_int(0, 9) >= 7 ? 7 - (random_int(0, 3) * 2) : null,
            'like_count' => random_int(0, 10),
            'unlike_count' => random_int(0, 3),
        ];
    }
}
