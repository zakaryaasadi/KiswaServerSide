<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GreenClosetLoginController extends Controller
{
    
    public function LoginIndex(){
        return view("login", ["errorLogin" => false] );
    }


    public function Login(Request $request){
         if($request->email == "x" && $request->password == "x"){
            return redirect("ehsan-report")->with('id', true);
         }

         return view("login", ["errorLogin" => true]);
    }
}
