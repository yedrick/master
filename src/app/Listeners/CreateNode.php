<?php

namespace yedrick\Master\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class CreateNode{
    public function handle($node){
        $saved = false;
        if(!$node->table_name){
            $node->table_name = str_replace('-','_',$node->name).'s';
            $saved = true;
        }
        if(!$node->model){
            $node->model = '\App\\Models\\'.str_replace('_','-',Str::studly($node->name));
            $saved = true;
        }
        if($saved===true){
            $node->save();
        }
    }
}
