<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->unique()->nullable()->after('name');
            $table->string('barcode')->unique()->nullable()->after('sku');
            $table->string('upc')->nullable()->after('barcode');
            $table->integer('reorder_point')->default(10)->after('quantity');
            $table->boolean('is_active')->default(true)->after('description');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'barcode', 'upc', 'reorder_point', 'is_active']);
        });
    }
};
