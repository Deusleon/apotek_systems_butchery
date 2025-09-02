<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvidenceDocumentToRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->string('evidence_document')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('evidence_document');
        });
    }
}