<?php

namespace yedrick\Master\Database\Seeders;

use yedrick\Master\Database\Seeders\MasterSeeder;
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
        // \App\Models\User::factory(10)->create();
        $this->call(MasterSeeder::class);
    }
}
