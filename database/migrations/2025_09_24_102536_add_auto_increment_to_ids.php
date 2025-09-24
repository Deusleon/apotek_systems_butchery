<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAutoIncrementToIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'sales',
            'sales_details',
            'sales_credits',
            'sales_quotes',
            'sales_quote_details',
            'stock_adjustment_logs',
            'sales_returns',
            'sales_prices',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `$table` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'sales',
            'sales_details',
            'sales_credits',
            'sales_quotes',
            'sales_quote_details',
            'stock_adjustment_logs',
            'sales_returns',
            'sales_prices',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `$table` MODIFY `id` BIGINT UNSIGNED NOT NULL");
        }
    }
}
