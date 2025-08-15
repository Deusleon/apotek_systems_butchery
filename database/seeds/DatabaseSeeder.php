<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call([
            PriceManagementPermissionsSeeder::class,
            StockPermissionsSeeder::class
        ]);
        $this->call(AdjustmentReasonSeeder::class);
    }
}
