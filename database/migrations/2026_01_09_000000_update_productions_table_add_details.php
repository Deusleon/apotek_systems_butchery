<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductionsTableAddDetails extends Migration
{
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->string('details')->nullable()->after('production_date');
            $table->decimal('meat', 10, 2)->default(0)->after('total_weight');
            $table->decimal('steak', 10, 2)->default(0)->after('meat');
            $table->decimal('beef_fillet', 10, 2)->default(0)->after('steak');
            $table->decimal('weight_difference', 10, 2)->default(0)->after('beef_fillet');
            $table->decimal('beef_liver', 10, 2)->default(0)->after('weight_difference');
        });

        // Remove the old meat_output column if it exists
        if (Schema::hasColumn('productions', 'meat_output')) {
            Schema::table('productions', function (Blueprint $table) {
                $table->dropColumn('meat_output');
            });
        }
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn(['details', 'meat', 'steak', 'beef_fillet', 'weight_difference', 'beef_liver']);
            $table->decimal('meat_output', 10, 2)->default(0);
        });
    }
}
