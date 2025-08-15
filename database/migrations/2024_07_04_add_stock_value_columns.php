<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStockValueColumns extends Migration
{
    public function up()
    {
        Schema::table('inv_current_stock', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_current_stock', 'stock_value')) {
                $table->decimal('stock_value', 15, 2)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('inv_current_stock', 'last_calculated_at')) {
                $table->timestamp('last_calculated_at')->nullable()->after('stock_value');
            }
        });
    }

    public function down()
    {
        Schema::table('inv_current_stock', function (Blueprint $table) {
            $table->dropColumn(['stock_value', 'last_calculated_at']);
        });
    }
} 