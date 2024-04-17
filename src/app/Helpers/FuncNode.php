<?php

namespace yedrick\Master\App\Helpers;

use yedrick\Master\App\Imports\ImportExcel;
use yedrick\Master\App\Imports\NodeImport;
use yedrick\Master\App\Models\Field;
use yedrick\Master\App\Models\FieldOption;
use yedrick\Master\App\Models\Node;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use \Maatwebsite\Excel\Reader;
use ReflectionClass;


class FuncNode {

    public static function getTables() {
        $ignoreTables = ['migrations', 'sessions', 'oauth_auth_codes', 'oauth_access_tokens', 'oauth_refresh_tokens', 'oauth_clients', 'oauth_personal_access_clients', 'personal_access_tokens'];
        $tables = [];
        $table_schema = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($table_schema as $table) {
            $tables[] = $table;
        }
        $tables = array_values( array_filter($tables, function($el) use( $ignoreTables ) {
			return !in_array( $el, $ignoreTables);
		}));
        // return $tables;
        \yedrick\Master\App\Helpers\FuncNode::createNodes($tables);
    }


    public static function createNodes($tablas) {
        foreach ($tablas as $key => $tabla) {
            $name=str_replace('_','-',Str::singular($tabla));
            $node=Node::create([
                'name'=>$name,
                'table_name'=>$tabla,
                'singular'=>'node.'.$name,
                'plural'=>'nodes.'.$name,
            ]);
        }
    }

    public static function createModels() {
        $nodes=Node::get();
        foreach ($nodes as $key => $node) {
            $table_name = $node->table_name;
            $model = str_replace('_','-',Str::studly(Str::singular($table_name)));
            // validar q no exista el modelo en el vendor
            $model_exist_master=base_path('vendor/yedrick/master/src/app/Models/'.$model.'.php');
            $model_exist=app_path('Models\\'.$model.'.php');
            if(!file_exists($model_exist) && !file_exists($model_exist_master)){
                Artisan::call('make:model-master', [
                        'table' => $table_name
                ]);
            }else{
                // \Log::info('si');
            }
        }
    }

    public static function getColumnMap($tabla) {
        $databaseName = config('database.connections.mysql.database');
        $columns = \DB::table('INFORMATION_SCHEMA.COLUMNS')
            ->select('COLUMN_NAME', 'DATA_TYPE','COLUMN_TYPE')
            ->where('TABLE_NAME', $tabla)
            ->where('TABLE_SCHEMA', $databaseName)
            ->get();
        return collect($columns)->map(function ($columna) {
                return [
                    'column' => $columna->COLUMN_NAME,
                    'type' => $columna->DATA_TYPE,
                    'data'=>$columna->COLUMN_TYPE
                ];
            });
    }

    public static function getForeignKeyInfo($table, $column){
        $databaseName = config('database.connections.mysql.database');
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            // Obtiene la información de las claves foráneas
            $foreignKeys = \DB::table('information_schema.KEY_COLUMN_USAGE')
                ->select('TABLE_NAME', 'COLUMN_NAME', 'REFERENCED_TABLE_NAME', 'REFERENCED_COLUMN_NAME')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', $column)
                ->first();

            return $foreignKeys;
        } else {
            return [];
        }
    }



    public static function creationNodeFields() {
        $nodes=Node::get();
        foreach ($nodes as $key => $node) {
            $table_name = $node->table_name;
            $columns=\yedrick\Master\App\Helpers\FuncNode::getColumnMap($table_name);
            $order=1;
            foreach ($columns as $key => $column) {
                // \Log::info($column);
                \yedrick\Master\App\Helpers\FuncNode::field_creation($node,$column,$order);
                $order++;
            }
        }
    }

    public static function field_creation($node,$column,$order) {
        $table_name = $node->table_name;
        $name=$column['column'];
        $col_type=$column['type'];
        $data_column=$column['data'];
        $model=$node->model;
        $type = 'text';
        $value = NULL;
        $label = 'field.'.$name;
        $trans_name = $name;
        $display_list = 'show';
        $display_item = 'show';
        $relation = false;
        $bolean=false;
        $enum=false;
        $parent=null;
        if($name=='id'){
            $display_list = 'excel';
            $display_item = 'excel';
        }elseif(strpos($name, '_id') !== false){
            $type = 'select';
            $relation = true;
            $trans_name = str_replace('_id', '', $name);
            $value = $trans_name;
            $model='\App\\Models\\'.str_replace('_','-',Str::studly($trans_name));
            if($name=='parent_id') {
                $referent=\yedrick\Master\App\Helpers\FuncNode::getForeignKeyInfo($table_name,$name);
                if($referent){
                    $parent=$value=$referent->REFERENCED_TABLE_NAME;
                }
            }
            $model='\App\\Models\\'.str_replace('_','-',Str::studly($value));
        }else if(\Str::contains(strtolower( $name ), 'image')){
            $type = 'image';
        }else if(\Str::contains(strtolower( $name ), 'file')){
            $type = 'file';
        }else if(\Str::contains(strtolower( $name ), 'color')){
            $type = 'color';
        }else if(\Str::contains(strtolower( $name ), 'email')){
            $type = 'email';
        }else if(\Str::contains(strtolower( $name ), 'password')){
            $type = 'password';
        }else if (in_array( $name, ['created_at', 'updated_at'] )) {
            $type='date';
            $display_list = 'excel';
            $display_item = 'excel';
        }else if(in_array( $name, ['phone', 'cellphone','cell','tel'] )){
            $type='tel';
            $bolean=true;
        }else if(\Str::contains(strtolower( $name ), 'url')){
            $type='url';
            $bolean=true;
        }else if($col_type=='boolean'){
            $type='select';
            $bolean=true;
        }else if($col_type=='tinyint'){
            $type='select';
            $bolean=true;
        }else if($col_type=='integer'){
            $type='number';
        }else if($col_type=='decimal'||\Str::contains(strtolower( $col_type ), 'decimal')){
            $type='decimal';
        }else if($col_type=='timestamp'||$col_type=='date'||$col_type=='timestamp'){
            $type='date';
        }else if($col_type=='enum'){
            $type='select';
            $enum=true;
        }else if($name=='deleted_at'){
            $node->soft_delete = 1;
            $node->save();
        }
        if( $order <= 5 ) {
            $display_list = 'show';
        }else{
            $display_list = 'excel';
        }
        // creamos el field
        $field = new Field();
        $field->parent_id = $node->id;
        $field->name = $name;
        $field->trans_name = $trans_name;
        $field->type = $type;
        $field->order = $order;
        $field->display_list = $display_list;
        $field->display_item = $display_item;
        $field->label= $label??"";
        // $field->translation = $translation;
        $field->relation = $relation;
        $field->value = $value;
        if( $relation  ) {
            $field->relation_cond = $model;
        }
        $field->save();
        if ($enum && $type='enum') {
            preg_match("/^enum\((.*)\)$/", $data_column, $matches);
            $enumOptions = explode("','", $matches[1]);
            foreach( $enumOptions as $option ) {
                $nwFieldOption = new \App\Models\FieldOption;
                $nwFieldOption->parent_id = $field->id;
                $nwFieldOption->name  = str_replace(['"', "'", '´'], '', $option);
                $nwFieldOption->label = trans('admin.'. str_replace(['"', "'", '´'], '', $option));
                $nwFieldOption->save();
            }
        }
        if($bolean){
            $nwFieldOption = new FieldOption();
            $nwFieldOption->parent_id = $field->id;
            $nwFieldOption->name  = 0;
            $nwFieldOption->label = 'No';
            $nwFieldOption->save();
            $nwFieldOption = new FieldOption();
            $nwFieldOption->parent_id = $field->id;
            $nwFieldOption->name  = 1;
            $nwFieldOption->label = 'Si';
            $nwFieldOption->save();
        }
        if($parent!=null && !in_array($parent,['nodes','fields','image_folders'])){
            \Log::info($parent);
            $search=Node::where('table_name',$parent)->first();
            if($search){
                $node->parent_id=$search->id;
                $node->type='child';
                $node->save();
            }
        }
    }



    // función para importar datos de excel

    public static function importExcel() {
        // $nodes=Node::get();
        // \Excel::import([],public_path('seed/import.xlsx'), function(Reader $reader){
        //     foreach($reader->get() as $sheet){
        //         $sheet_model = $sheet->getTitle();
        //         \Log::info($sheet);
        //         \Log::info($sheet_model);
        //         // \DataManager::importExcelRows($sheet, $nodes);
        //     }
        // });
        // $nodes = Node::get();

        // $excel = \Excel::import(new ImportExcel($nodes), public_path('seed/import.xlsx'));
        // \Log::info('excel');
        // \Log::info($excel);
        // $sheets = \Excel::import(new NodeImport(), public_path('seed/import.xlsx'));

        // Recorrer la información de las hojas
        $sheetsInfo = \Excel::getSheetNames(public_path('seed/import.xlsx'));

            // Recorrer la información de las hojas
            foreach ($sheetsInfo as $sheetTitle) {
                // Realiza las operaciones necesarias con el título de la hoja
                echo $sheetTitle . "\n";
            }

        // $data = \Excel::toCollection([], public_path('seed/import.xlsx'));
        // \Log::info($data);
        // $sheets = \Excel::import(null,public_path('seed/import.xlsx'));

        // // Obtenemos los nombres de las hojas
        // $sheetNames = $sheets->sheetNames();
    }


}
