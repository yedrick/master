<?php

namespace Mastery\Master\App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model{

    protected $table = 'password_resets';
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

}
