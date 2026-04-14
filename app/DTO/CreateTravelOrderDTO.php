<?php

namespace App\DTO;

final class CreateTravelOrderDTO
{
    public function __construct(
        public readonly string $destination,
        public readonly string $departure_date,
        public readonly string $return_date,
    ) {}
}
