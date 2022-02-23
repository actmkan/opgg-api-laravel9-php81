<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {

        return [
            'title' => $this->faker->realText(30),
            'content' => $this->faker->realText(3000),
            'user_id' =>random_int(1, 9),
            'like_count' => random_int(0, 30),
            'unlike_count' => random_int(0, 10),
            'view_count' => random_int(0, 1000),

        ];
    }
}
