<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    public function hospitalizations()
    {
        return $this->hasMany(Hospitalization::class);
    }

}
