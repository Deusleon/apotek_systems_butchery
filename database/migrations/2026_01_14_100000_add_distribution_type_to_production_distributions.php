<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistributionTypeToProductionDistributions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_distributions', function (Blueprint $table) {
            $table->string('distribution_type')->default('branch')->after('production_id');
            // Add customer_id for cash sales
            $table->unsignedBigInteger('customer_id')->nullable()->after('store_id');
            // Add order_id for order distributions
            $table->unsignedBigInteger('order_id')->nullable()->after('customer_id');
            // Add optional notes
            $table->text('notes')->nullable()->after('weight_distributed');
        });
        
        // Make store_id nullable using raw SQL (to avoid doctrine/dbal dependency)
        \DB::statement('ALTER TABLE production_distributions MODIFY store_id INT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_distributions', function (Blueprint $table) {
            $table->dropColumn(['distribution_type', 'customer_id', 'order_id', 'notes']);
        });
    }
}
