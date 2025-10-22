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
        Schema::table('order_items', function (Blueprint $table) {
            // Add new columns for shopping cart functionality
            $table->string('product_name')->after('product_id');
            $table->decimal('unit_price', 10, 2)->after('quantity');
            $table->decimal('total_price', 10, 2)->after('unit_price');
            
            // Rename price column to avoid confusion
            $table->renameColumn('price', 'old_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'product_name',
                'unit_price',
                'total_price'
            ]);
            
            // Restore original column name
            $table->renameColumn('old_price', 'price');
        });
    }
};
