<?php

namespace App\Http\Resources;

use App\Domain\TravelOrder;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    public function __construct(private TravelOrder $travelOrder) {}

    public function toArray($request): array
    {
        return [
            'id'             => $this->travelOrder->id,
            'requester_name' => $this->travelOrder->requester_name,
            'destination'    => $this->travelOrder->destination,
            'departure_date' => $this->travelOrder->departure_date,
            'return_date'    => $this->travelOrder->return_date,
            'status'         => $this->travelOrder->status->value,
            'created_at'     => $this->travelOrder->created_at ?? null,
            'updated_at'     => $this->travelOrder->updated_at ?? null,
        ];
    }
}
