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
					Schema::create('posts', function (Blueprint $table) {
						$table->id();
						$table->unsignedBigInteger('com_id');
						$table->unsignedBigInteger('dep_id');
						$table->string('name');
						$table->text('permission')->nullable();
						$table->timestamps();

						// Добавляем внешний ключ
						$table->foreign('com_id')->references('id')->on('companies');

						// Добавляем внешний ключ к таблице departments
						$table->foreign('dep_id')->references('id')->on('departments');
				});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
