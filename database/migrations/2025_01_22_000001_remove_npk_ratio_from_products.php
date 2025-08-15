<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNpkRatioFromProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_products', function (Blueprint $table) {
            $table->dropColumn('npk_ratio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_products', function (Blueprint $table) {
            $table->string('npk_ratio', 15)->nullable()->after('updated_at');
        });
    }
} 