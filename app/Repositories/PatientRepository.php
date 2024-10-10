<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Models\Patient;

class PatientRepository
{
    public $model;

    public function __construct(Patient $model)
    {
        $this->model = $model;
    }

    public function getByCode($code)
    {
        return $this->model->where('code', $code)
                           ->first();
    }
    
    public function getByNameBirth($name, $birth)
    {
        return $this->model->where([
                                ['name', $name],
                                ['birth', $birth],
                            ])
                            ->with('hospitalizations')
                            ->first();
    }

    public function isExistPatient($code, $name, $birth)
    {
        return $this->model->where([
                            ['code', $code],
                            ['name', $name],
                            ['birth', $birth],
                        ])->exists();
    }

    
}