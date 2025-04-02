<?php

namespace Bishopm\Methodist\Http\Controllers;

class HomeController extends Controller
{

    public function home()
    {
        $data=array();
        return view('methodist::web.home',$data);
    }

    public function editplan()
    {
        return view('methodist::edit-plan');
    }


}
