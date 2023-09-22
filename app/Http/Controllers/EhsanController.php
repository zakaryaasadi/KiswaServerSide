<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EhsanController extends Controller
{
    public function Index(){
        return view("ehsan_report");
    }
}
