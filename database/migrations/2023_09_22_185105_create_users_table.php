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
						$table->string('login')->unique();
						$table->string('role');
            $table->string('password');
            $table->rememberToken();
						$table->unsignedBigInteger('com_id')->nullable();
						$table->unsignedBigInteger('dep_id')->nullable();
						$table->unsignedBigInteger('post_id')->nullable();
						$table->string('full_name')->nullable();
						$table->string('phone_number')->nullable();
						$table->string('stage')->nullable();
						$table->string('tg_client_id')->nullable();
						$table->timestamps();

						// Добавляем внешний ключ к таблице companies
						$table->foreign('com_id')->references('id')->on('companies');

						// Добавляем внешний ключ к таблице departments
						$table->foreign('dep_id')->references('id')->on('departments');

						// Добавляем внешний ключ к таблице posts
						$table->foreign('post_id')->references('id')->on('posts');
        });

				// Вставка Администратора сразу после создания таблицы
				DB::table('users')->insert([
						'login' => 'admin',
						'type' => 'admin',
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
