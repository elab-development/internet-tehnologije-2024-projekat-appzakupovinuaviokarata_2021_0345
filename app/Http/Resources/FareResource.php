<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FareResource extends JsonResource
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
            'cabin_class' => $this->cabin_class,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'available_seats' => $this->available_seats,
            'rules' => $this->rules,
        ];
    }
}
