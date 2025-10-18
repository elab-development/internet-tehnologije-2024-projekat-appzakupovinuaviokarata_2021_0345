<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $guarded = [];
    protected $casts = ['contact'=>'array'];
    public function user()   { return $this->belongsTo(User::class); }
    public function flight() { return $this->belongsTo(Flight::class); }
    public function fare()   { return $this->belongsTo(Fare::class); }
}
