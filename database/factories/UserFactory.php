<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'login' => $this->faker->unique()->userName,
            'role' => 'user', // default role
            'password' => Hash::make('password'), // default password
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
