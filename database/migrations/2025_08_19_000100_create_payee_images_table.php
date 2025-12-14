<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('payee_images', function (Blueprint $table) {
			$table->id();
			$table->foreignId('payee_id')->constrained('payees')->onDelete('cascade');
			$table->string('path');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('payee_images');
	}
}; 