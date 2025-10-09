<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('goods_receiving_id');
            $table->decimal('quantity', 10, 2);
            $table->text('reason');
            $table->dateTime('date');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('goods_receiving_id')->references('id')->on('inv_incoming_stock');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_returns');
    }
}
