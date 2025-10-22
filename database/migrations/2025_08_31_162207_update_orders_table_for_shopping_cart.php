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
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns for shopping cart functionality
            $table->string('order_number')->unique()->after('id');
            $table->decimal('subtotal', 10, 2)->after('total_price');
            $table->decimal('tax', 10, 2)->default(0)->after('subtotal');
            $table->decimal('shipping', 10, 2)->default(0)->after('tax');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->change();
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('stripe_session_id')->nullable()->after('payment_method');
            $table->text('shipping_address')->nullable()->after('stripe_session_id');
            $table->text('billing_address')->nullable()->after('shipping_address');
            $table->string('tracking_number')->nullable()->after('billing_address');
            $table->text('notes')->nullable()->after('tracking_number');
            $table->timestamp('paid_at')->nullable()->after('notes');
            $table->timestamp('shipped_at')->nullable()->after('paid_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            
            // Drop the club_id foreign key constraint first, then the column
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'order_number',
                'subtotal',
                'tax',
                'shipping',
                'payment_status',
                'payment_method',
                'stripe_session_id',
                'shipping_address',
                'billing_address',
                'tracking_number',
                'notes',
                'paid_at',
                'shipped_at',
                'delivered_at'
            ]);
            
            // Restore original columns
            $table->unsignedBigInteger('club_id')->nullable()->after('user_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->change();
        });
    }
};
