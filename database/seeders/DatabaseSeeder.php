<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
					 'login' => 'admin',
					 'role' => 'admin',
					 'password' => function() {
						print 'Введите пароль Администратора';
						return Hash::make(fgets(STDIN));
					 },
					 'remember_token' => null,
					 'created_at' => '2023-09-24 12:26:05',
					 'updated_at' => '2023-09-24 12:26:05',
        ]);
    }
}
