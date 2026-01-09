<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
   
    public function sidebar(){
        return view('layout.sidebar');
    }
}
