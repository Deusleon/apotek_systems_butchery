<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_distributions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('production_id');
            $table->integer('store_id');
            $table->string('meat_type');
            $table->decimal('weight_distributed', 10, 2);
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_distributions');
    }
}
