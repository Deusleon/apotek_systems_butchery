<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOrderIdToOrderToInProductionDistributions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to rename and change column type (to avoid doctrine/dbal dependency)
        \DB::statement('ALTER TABLE production_distributions CHANGE order_id order_to VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE production_distributions CHANGE order_to order_id BIGINT UNSIGNED NULL');
    }
}
