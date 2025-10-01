<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the expense request seeder
        // $this->call(ExpenseRequestSeeder::class);
        
        echo 'Введите пароль Администратора: ';
        $password = trim(fgets(STDIN));
        
        User::create([
            'login' => 'admin',
            'role' => 'admin',
            'in_bot_role' => 'user',
            'status' => 'active',
            'password' => Hash::make($password),
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}