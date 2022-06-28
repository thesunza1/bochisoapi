<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class BscController extends Controller
{
    //
    public function testdate(Request $request) {
       $day = new Carbon($request->dates) ;

       return $day->toDateString();
    }
}
