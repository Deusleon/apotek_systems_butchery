<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvOldStockValueTable extends Migration
{
    public function up()
    {
        Schema::create('inv_old_stock_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->decimal('quantity', 20, 4)->default(0);
            $table->decimal('buy_price', 20, 4)->default(0);   // unit_cost snapshot (latest)
            $table->decimal('sell_price', 20, 4)->default(0);  // price from sales_prices (latest per stock_id & price_category)
            $table->unsignedBigInteger('price_category_id')->nullable();
            $table->date('snapshot_date'); // date snapshot (YYYY-MM-DD)
            $table->timestamps();

            $table->index(['product_id','store_id','snapshot_date','price_category_id'], 'oldstock_idx');
            $table->foreign('product_id')->references('id')->on('inv_products')->onDelete('cascade');
            $table->foreign('price_category_id')->references('id')->on('price_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inv_old_stock_value');
    }
}
