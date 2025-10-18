<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Flight extends Model
{
    protected $guarded = [];
    protected $casts = [
        'dep_time' => 'datetime',
        'arr_time' => 'datetime',
    ];

    public function carrier()      { return $this->belongsTo(Carrier::class); }
    public function origin()       { return $this->belongsTo(Airport::class, 'airport_from_id'); }
    public function destination()  { return $this->belongsTo(Airport::class, 'airport_to_id'); }
    public function fares()        { return $this->hasMany(Fare::class); }
    public function bookings()     { return $this->hasMany(Booking::class); }


}


