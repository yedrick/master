<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class TruncateSeeder extends Seeder {
        
        public function run(){
            $tables = DB::select('SHOW TABLES');
            foreach($tables as $table) {
                $table_array = get_object_vars($table);
                $table_name = $table_array[key($table_array)];
                if($table_name!='migrations'){
                    DB::table($table_name)->truncate();
                }
            }
        }

}