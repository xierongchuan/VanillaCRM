<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('reports', function (Blueprint $table) {
			$table->id();
			$table->string('type');
			$table->unsignedBigInteger('com_id')->nullable();
			$table->text('data');
			$table->date('for_date');
			$table->timestamps();

			$table->foreign('com_id')->references('id')->on('companies');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('reports');
	}
};
