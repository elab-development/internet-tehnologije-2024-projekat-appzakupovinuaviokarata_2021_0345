<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
             'id' => $this->id,
            'flight_no' => $this->flight_no,
            'dep_time' => $this->dep_time->toIso8601String(),
            'arr_time' => $this->arr_time->toIso8601String(),
            'duration_min' => $this->duration_min,
            'stops' => $this->stops,
            'status'    => $this->status ?? null,

            'from' => new AirportResource($this->whenLoaded('origin')),
            'to'   => new AirportResource($this->whenLoaded('destination')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),

            
            'fares' => FareResource::collection($this->whenLoaded('fares')),
            'cheapest_fare' => $this->fares->min('price') ?? null,
        ];

        
    }

    protected $casts = ['dep_time' => 'datetime','arr_time' => 'datetime'];
}
