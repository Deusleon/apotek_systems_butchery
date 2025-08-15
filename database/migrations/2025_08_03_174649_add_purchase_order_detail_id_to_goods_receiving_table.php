<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseOrderDetailIdToGoodsReceivingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_incoming_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_detail_id')->nullable()->after('invoice_no');

            // If you want to add a foreign key constraint (recommended)
            // $table->foreign('purchase_order_detail_id')->references('id')->on('order_details')->onDelete('set null');
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
            $table->dropColumn('purchase_order_detail_id');
        });
    }
}
