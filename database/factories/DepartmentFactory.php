<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'com_id' => Company::factory(),
            'name' => $this->faker->word,
        ];
    }
}
