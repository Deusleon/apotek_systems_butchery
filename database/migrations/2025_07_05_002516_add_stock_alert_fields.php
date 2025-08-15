<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStockAlertFields extends Migration
{
    public function up()
    {
        Schema::table('inv_current_stock', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_current_stock', 'min_stock_level')) {
                $table->decimal('min_stock_level', 10, 2)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('inv_current_stock', 'max_stock_level')) {
                $table->decimal('max_stock_level', 10, 2)->nullable()->after('min_stock_level');
            }
            if (!Schema::hasColumn('inv_current_stock', 'alert_threshold')) {
                $table->decimal('alert_threshold', 10, 2)->nullable()->after('max_stock_level');
            }
            if (!Schema::hasColumn('inv_current_stock', 'last_alert_sent_at')) {
                $table->timestamp('last_alert_sent_at')->nullable()->after('alert_threshold');
            }
        });
    }

    public function down()
    {
        Schema::table('inv_current_stock', function (Blueprint $table) {
            $table->dropColumn([
                'min_stock_level',
                'max_stock_level',
                'alert_threshold',
                'last_alert_sent_at'
            ]);
        });
    }
} 