<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ExpenseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample companies if they don't exist
        $companies = [
            ['id' => 1, 'name' => 'Компания 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Компания 2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Компания 3', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        
        foreach ($companies as $company) {
            DB::table('companies')->updateOrInsert(
                ['id' => $company['id']],
                $company
            );
        }
        
        // Create sample users
        $users = [
            [
                'login' => 'admin',
                'password' => Hash::make('password123'),
                'full_name' => 'Admin User',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'login' => 'user1',
                'password' => Hash::make('password123'),
                'full_name' => 'Regular User',
                'role' => 'user',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['login' => $user['login']],
                $user
            );
        }
    }
}