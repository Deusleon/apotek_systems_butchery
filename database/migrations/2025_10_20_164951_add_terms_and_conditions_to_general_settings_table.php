<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermsAndConditionsToGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('cash_sale_terms')->nullable();
            $table->text('credit_sale_terms')->nullable();
            $table->text('proforma_invoice_terms')->nullable();
            $table->text('purchase_order_terms')->nullable();
            $table->text('delivery_note_terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['cash_sale_terms', 'credit_sale_terms', 'proforma_invoice_terms', 'purchase_order_terms']);
        });
    }
}
