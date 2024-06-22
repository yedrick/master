<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Master extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('table_name')->nullable();
            $table->string('model')->nullable();
            $table->string('singular')->nullable();
            $table->string('plural')->nullable();
            $table->enum('type', ['normal', 'child', 'subchild', 'field'])->default('normal');
            $table->string('folder')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('fields',function(Blueprint $table){
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('order')->nullable()->default(0);
            $table->string('name');
            $table->string('trans_name');
            $table->enum('type', ['string','integer','decimal','text','select','password','email','url','image','file','barcode','map','color','radio','checkbox','date','array','score','hidden','child','subchild','field','custom','title','content'])->default('string');
            $table->enum('display_list', ['show', 'excel', 'none'])->default('show');
            $table->enum('display_item', ['show', 'excel','none'])->default('show');
            $table->boolean('relation')->default(0);
            // $table->boolean('multiple')->default(0);
            // $table->boolean('translation')->default(0);
            $table->boolean('required')->default(0);
            // $table->boolean('new_row')->default(0);
            // $table->boolean('preset')->default(0);
            $table->string('label')->nullable();
            // $table->string('permission')->nullable();
            $table->string('placeholder')->nullable();

            $table->string('child_table')->nullable();
            $table->string('relation_cond')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('nodes')->onDelete('cascade');
        });

        Schema::create('field_options',function(Blueprint $table){
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->string('name');
            $table->string('label');
            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('fields')->onDelete('cascade');
        });

        Schema::create('image_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('extension', ['jpg','png','gif'])->default('jpg');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('image_sizes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(1);
            $table->string('code');
            $table->enum('type', ['original','resize','fit'])->default('original');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->foreign('parent_id')->references('id')->on('image_folders')->onDelete('cascade');
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->nullable();
            $table->integer('level')->nullable()->default(1);
            $table->integer('order')->nullable()->default(0);
            $table->boolean('active')->nullable()->default(1);
            $table->enum('menu_type', ['site', 'customer', 'admin'])->default('site');
            $table->enum('type', ['normal', 'external', 'blank'])->default('normal');
            $table->string('permission')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('menu_translation', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('link')->nullable();
            $table->unique(['menu_id','locale']);
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nodes');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('field_options');
        Schema::dropIfExists('image_folders');
        Schema::dropIfExists('image_sizes');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('menu_translation');
    }
}
