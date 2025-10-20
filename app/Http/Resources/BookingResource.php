<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{

    
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'created_at'  => $this->created_at?->toDateTimeString(),
            'flight'      => new FlightResource($this->whenLoaded('flight')),
            'user'        => [
                'id' => $this->user_id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
        ];
    }
}
