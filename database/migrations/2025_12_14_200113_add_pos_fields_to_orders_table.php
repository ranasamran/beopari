<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add customer relationship
            $table->foreignId('customer_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            
            // Add order status
            $table->enum('status', ['pending', 'completed', 'void', 'refunded'])->default('pending')->after('number');
            
            // Add tax fields
            $table->decimal('subtotal', 15, 2)->default(0)->after('status');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
            $table->foreignId('tax_rate_id')->nullable()->after('tax_amount')->constrained('tax_rates')->onDelete('set null');
            
            // Make discount nullable and reorder
            $table->decimal('discount', 15, 2)->default(0)->change();
            
            // Add audit fields
            $table->foreignId('created_by')->nullable()->after('payable')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->softDeletes();
            
            // Add indexes
            $table->index('customer_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['tax_rate_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'customer_id', 'status', 'subtotal', 
                'tax_amount', 'tax_rate_id', 'created_by', 'updated_by'
            ]);
        });
    }
};
