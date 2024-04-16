<?php

namespace yedrick\Master\App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\NodeTrait;

class Product extends Model{

    use NodeTrait;
    protected $table = 'products';
    protected $node_name='product';
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


    // Definir relaciones y atributos aquí
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    // public function fromDataModel(){
    //     return[
    //         'id'=>$this->id,
    //     ];
    // }
}
