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
        Schema::table('products', function (Blueprint $table) {
             $table->double('discounted_price', 15, 2)->default(0)->after('retail_price');
            $table->string('net_weight')->nullable()->after('discounted_price');
            $table->string('category')->nullable()->after('net_weight');
            $table->text('note')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discounted_price', 'net_weight', 'category', 'note']);
        });
    }
};
