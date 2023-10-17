<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

//         User::factory()->create([
//					 'login' => 'admin',
//					 'role' => 'admin',
//					 'password' => '$2y$10$aUu5mi2aquDAIo97E4fmJOyUqzaiP7B0m3bE.w0Nu8Wtn8GK7SneK',
//					 'remember_token' => null,
//					 'created_at' => '2023-09-24 12:26:05',
//					 'updated_at' => '2023-09-24 12:26:05',
//         ]);

				// Вставка Администратора сразу после создания таблицы
				DB::table('users')->insert([
					'login' => 'admin',
					'role' => 'admin',
					'password' => '$2y$10$aUu5mi2aquDAIo97E4fmJOyUqzaiP7B0m3bE.w0Nu8Wtn8GK7SneK',
					'remember_token' => null,
					'created_at' => '2023-09-24 12:26:05',
					'updated_at' => '2023-09-24 12:26:05',
				]);
    }
}
