<?php

namespace App\DTO;

use App\Enums\TravelStatus;

final class UpdateTravelOrderStatusDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
    ) {}
}
