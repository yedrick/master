<?php

namespace yedrick\Master\App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller{

    //TODO: Implementar CONSTRUCT CON EL MIDDLEWARE
    public function __construct(){
        $this->middleware('auth');
    }

    public function getTest() {
        $product=\App\Models\Product::find(1);
        dd($product->fromDataModel());
        // dd($user->map->fromData());
    }

}
