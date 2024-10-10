<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Models\Hospitalization;

class HospitalizationRepository
{
    public $model;

    public function __construct(Hospitalization $model)
    {
        return $this->model = $model;
    }

    public function getByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }

    
}