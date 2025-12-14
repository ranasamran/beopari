<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('payees', function (Blueprint $table) {
			$table->date('date')->nullable()->after('image_path');
			$table->date('order_date')->nullable()->after('date');
			$table->date('delivery_date')->nullable()->after('order_date');
		});
	}

	public function down(): void
	{
		Schema::table('payees', function (Blueprint $table) {
			$table->dropColumn(['date', 'order_date', 'delivery_date']);
		});
	}
}; 