<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExtrasController extends Controller
{
    public function bonus()
    {
        return view('extras.bonus');
    }

    public function extension()
    {
        return view('extras.extension');
    }

    public function software()
    {
        return view('extras.software');
    }
}
