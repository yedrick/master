<?php

namespace yedrick\Master\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model {
    use HasFactory;

    protected $table='nodes';
    protected $with = ['parent'];
    protected $fillable = ['name', 'table_name', 'model','singular','plural'];
    public $timestamps=true;


    //model relations
    public function children(){
        return $this->hasMany(Node::class,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo(Node::class,'parent_id');
    }

    public function fields(){
        return $this->hasMany(Fields::class,'parent_id')->orderBy('order', 'ASC');
    }

    // public function node_extras(){
    //     return $this->hasMany(NodeExtra::class,'parent_id')->orderBy('order', 'ASC');
    // }

    // public function node_action_fields(){
    //     return $this->hasMany(NodeActionField::class,'parent_id')->orderBy('order', 'ASC');
    // }


}
