<?php

namespace Bishopm\Methodist\Http\Controllers;

class HomeController extends Controller
{

    public function home()
    {
        $data=array();
        return view('methodist::home',$data);
    }


}
