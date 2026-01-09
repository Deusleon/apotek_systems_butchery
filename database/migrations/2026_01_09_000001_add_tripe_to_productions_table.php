<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTripeToProductionsTable extends Migration
{
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->decimal('tripe', 10, 2)->default(0)->after('beef_liver');
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('tripe');
        });
    }
}
