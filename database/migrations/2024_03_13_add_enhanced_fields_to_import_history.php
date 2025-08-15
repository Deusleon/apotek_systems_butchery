<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhancedFieldsToImportHistory extends Migration
{
    public function up()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('created_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->integer('processing_time')->nullable()->after('completed_at');
            $table->float('progress')->default(0)->after('status');
            $table->json('metadata')->nullable()->after('error_log');
            $table->json('processed_rows')->nullable()->after('metadata');
            $table->json('final_summary')->nullable()->after('processed_rows');
            // Modify status column to support new states
            $table->string('status')->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->dropColumn([
                'started_at',
                'completed_at',
                'processing_time',
                'progress',
                'metadata',
                'processed_rows',
                'final_summary'
            ]);
            // Revert status column
            $table->string('status')->default('processing')->change();
        });
    }
} 