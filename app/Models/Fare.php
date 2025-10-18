<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fare extends Model
{
    protected $guarded = [];
    protected $casts = [
        'rules' => 'array',
    ];
}
