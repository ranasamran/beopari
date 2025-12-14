<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payee_trans', function (Blueprint $table) {
            $table->id('trans_id');
            $table->string('name');
            $table->foreignId('cus_id')->constrained('payees')->onDelete('cascade');
            $table->double('amount', 15, 2);
            $table->double('remain_amount', 15, 2);
            $table->integer('status'); // 0 for in, 1 for out
            $table->dateTime('datetime');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payee_trans');
    }
}; 