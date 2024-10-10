<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function validateData(Request $request)
    {
        dd($request->items);
    }
}
