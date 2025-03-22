<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //				Schema::create('workers', function (Blueprint $table) {
        //						$table->id();
        //						$table->unsignedBigInteger('com_id');
        //						$table->unsignedBigInteger('dep_id');
        //						$table->unsignedBigInteger('post_id')->nullable();
        //						$table->string('full_name');
        //						$table->string('phone_number');
        //						$table->string('stage')->nullable();
        //						$table->timestamps();
        //
        //						// Добавляем внешний ключ к таблице companies
        //						$table->foreign('com_id')->references('id')->on('companies');
        //
        //						// Добавляем внешний ключ к таблице departments
        //						$table->foreign('dep_id')->references('id')->on('departments');
        //
        //						// Добавляем внешний ключ к таблице posts
        //						$table->foreign('post_id')->references('id')->on('posts');
        //				});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //        Schema::dropIfExists('workers');
    }
};
