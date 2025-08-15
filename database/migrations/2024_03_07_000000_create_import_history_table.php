<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('import_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_name');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('price_category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->integer('total_records')->default(0);
            $table->integer('successful_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->text('error_log')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('inv_stores');
            $table->foreign('price_category_id')->references('id')->on('price_categories');
            $table->foreign('supplier_id')->references('id')->on('inv_suppliers');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_history');
    }
} 