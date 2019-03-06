<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * IndexController
 */
class IndexController extends Controller
{
    /**
    * Instantiate a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Invoke single action controller.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke($path = null)
    {
        return response()->view('index');
    }
}
