<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceManagementFields extends Migration
{
    public function up()
    {
        Schema::table('sales_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_prices', 'is_custom')) {
                $table->boolean('is_custom')->default(false)->after('price');
            }
            if (!Schema::hasColumn('sales_prices', 'default_markup_percentage')) {
                $table->decimal('default_markup_percentage', 5, 2)->nullable()->after('is_custom');
            }
            if (!Schema::hasColumn('sales_prices', 'override_reason')) {
                $table->string('override_reason')->nullable()->after('default_markup_percentage');
            }
            if (!Schema::hasColumn('sales_prices', 'override_by')) {
                $table->unsignedBigInteger('override_by')->nullable()->after('override_reason');
                $table->foreign('override_by')->references('id')->on('users');
            }
        });

        Schema::table('price_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('price_categories', 'default_markup_percentage')) {
                $table->decimal('default_markup_percentage', 5, 2)->nullable()->after('name');
            }
        });
    }

    public function down()
    {
        Schema::table('sales_prices', function (Blueprint $table) {
            $table->dropForeign(['override_by']);
            $table->dropColumn([
                'is_custom',
                'default_markup_percentage',
                'override_reason',
                'override_by'
            ]);
        });

        Schema::table('price_categories', function (Blueprint $table) {
            $table->dropColumn('default_markup_percentage');
        });
    }
} 