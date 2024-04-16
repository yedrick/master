<?php

namespace Mastery\Master\App\Listeners;

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
            // if($node->location=='package'){
            //     $node->model = '\Solunes\Master\App\\'.str_replace('_','-',studly_case($node->name));
            // } else if($node->location=='app') {
            //     $node->model = '\App\\'.str_replace('_','-',studly_case($node->name));
            // } else if(strpos($node->folder, 'todotix') !== false) {
            //     $node->model = '\Todotix\\'.ucfirst($node->location).'\App\\'.str_replace('_','-',studly_case($node->name));
            // } else {
            //     $node->model = '\Solunes\\'.ucfirst($node->location).'\App\\'.str_replace('_','-',studly_case($node->name));
            // }
            $node->model = '\App\\Models\\'.str_replace('_','-',Str::studly($node->name));
            $saved = true;
        }
        if($saved===true){
            $node->save();
        }
    }
}
