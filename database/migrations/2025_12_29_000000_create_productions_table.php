<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration

{

    public function up()

    {

        Schema::create('productions', function (Blueprint $table) {

            $table->increments('id');

            $table->date('production_date');

            $table->integer('cows_received');

            $table->decimal('total_weight', 10, 2);

            $table->decimal('meat_output', 10, 2);

            $table->timestamps();

        });

    }

    public function down()

    {

        Schema::dropIfExists('productions');

    }

}