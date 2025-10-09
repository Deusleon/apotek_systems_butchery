<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToInvIncomingStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_incoming_stock', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('created_by');
            // 1 = normal, 2 = pending return, 3 = approved return, 4 = rejected return, 5 = partial return
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_incoming_stock', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
