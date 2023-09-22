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
						$table->string('name');
						$table->string('permission');
						$table->timestamps();

						// Добавляем внешний ключ
						$table->foreign('com_id')->references('id')->on('companies');
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
