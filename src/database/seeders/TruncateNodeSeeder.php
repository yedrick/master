<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class TruncateNodeSeeder extends Seeder {
        
        public function run(){
            \yedrick\Master\App\Models\Field::truncate();
            \yedrick\Master\App\Models\Node::truncate();
            \yedrick\Master\App\Models\FieldOption::truncate();
            \yedrick\Master\App\Models\Menu::truncate();
            \yedrick\Master\App\Models\MenuTranslation::truncate();
            \yedrick\Master\App\Models\ImageFolder::truncate();
            \yedrick\Master\App\Models\ImageSize::truncate();

        }

}