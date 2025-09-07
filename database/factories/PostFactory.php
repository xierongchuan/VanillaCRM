<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'com_id' => Company::factory(),
            'dep_id' => Department::factory(),
            'name' => $this->faker->word,
        ];
    }
}
