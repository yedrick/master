<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use DB;

class TruncateNodeSeeder extends Seeder {
        
        public function run(){
            Schema::disableForeignKeyConstraints();
            DB::table('nodes')->truncate();
            DB::table('fields')->truncate();
            DB::table('field_options')->truncate();
            DB::table('menus')->truncate();
            DB::table('menu_translations')->truncate();
            DB::table('image_folders')->truncate();
            DB::table('image_sizes')->truncate();
            Schema::enableForeignKeyConstraints();

        }

}