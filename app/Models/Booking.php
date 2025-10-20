<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Booking extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['contact'=>'array'];
    public function user()   { return $this->belongsTo(User::class); }
    public function flight() { return $this->belongsTo(Flight::class); }
    public function fare()   { return $this->belongsTo(Fare::class); }
}
