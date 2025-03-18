<?php

namespace Bishopm\Church\Http\Controllers;

class HomeController extends Controller
{

    public function home(FormRequest $request)
    {
        return view('church::home',$data);
    }


}
