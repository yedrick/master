<?php

namespace Mastery\Master\App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model{

    protected $table = 'fields';
    protected $with = [];
    public $timestamps=true;

        /* Create rules */
    public static $rules_create = array(
	);
		/* Updating rules */
    public static $rules_edit = array(
        "id"=>"required",

    );
        /* Read rules */
    public static $rules_read = array(
        "id"=>"required",
    );
        /* Delete rules */
    public static $rules_remove = array(
        "id"=>"required",
    );

    public function children(){
        return $this->hasMany(Node::class,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo(Node::class,'parent_id');
    }


    // Definir relaciones y atributos aquí

}
