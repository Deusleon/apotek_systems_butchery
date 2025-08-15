<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCompletedAtInImportHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->change();
            $table->timestamp('started_at')->nullable()->change();
            $table->decimal('processing_time', 10, 2)->nullable()->change();
            $table->text('final_summary')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable(false)->change();
            $table->timestamp('started_at')->nullable(false)->change();
            $table->decimal('processing_time', 10, 2)->nullable(false)->change();
            $table->text('final_summary')->nullable(false)->change();
        });
    }
}
