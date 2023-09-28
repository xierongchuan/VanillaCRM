<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

				// Вставка Администратора сразу после создания таблицы
				DB::table('users')->insert([
						'login' => 'admin',
						'password' => '$2y$10$aUu5mi2aquDAIo97E4fmJOyUqzaiP7B0m3bE.w0Nu8Wtn8GK7SneK',
						'remember_token' => null,
						'created_at' => '2023-09-24 12:26:05',
						'updated_at' => '2023-09-24 12:26:05',
				]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
