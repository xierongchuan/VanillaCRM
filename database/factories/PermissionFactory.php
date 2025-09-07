<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition()
    {
        return [
            'com_id' => Company::factory(),
            'name' => $this->faker->word,
            'value' => $this->faker->word . '.' . $this->faker->word,
        ];
    }
}
