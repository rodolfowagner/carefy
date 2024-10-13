<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;
use App\Models\Hospitalization;

class IndexController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $totalHospitalizations = Hospitalization::count();

        return view('index', compact('totalPatients', 'totalHospitalizations'));
    }
}
