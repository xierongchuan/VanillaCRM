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
				Schema::create('departments', function (Blueprint $table) {
						$table->id();
						$table->unsignedBigInteger('com_id');
						$table->string('name');
						$table->timestamps();

						// Добавляем внешний ключ к таблице companies
						$table->foreign('com_id')->references('id')->on('companies');
				});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
