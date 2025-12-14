<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->double('gross_total', 15, 2);
            $table->double('discount', 15, 2);
            $table->double('total_paid', 15, 2);
            $table->double('balance', 15, 2);
            $table->string('tyre');
            $table->string('customer');
            $table->string('number');
            $table->double('payable', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 