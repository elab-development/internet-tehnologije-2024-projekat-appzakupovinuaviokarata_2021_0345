<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fare extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'rules' => 'array',
    ];

    protected $fillable = [
        'flight_id','cabin_class','price','currency','baggage','refundable','change_fees',
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }
}
