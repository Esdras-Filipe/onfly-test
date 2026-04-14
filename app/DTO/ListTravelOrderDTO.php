<?php

namespace App\DTO;

final class ListTravelOrderDTO
{
    public function __construct(
        public readonly ?string $destination,
        public readonly ?string $status,
        public readonly ?string $sortBy,
        public readonly ?string $sortDirection,
        public readonly ?int $perPage,
        public readonly ?int $page,
        public readonly ?string $departure_date_from,
        public readonly ?string $departure_date_to,
        public readonly ?string $return_date_from,
        public readonly ?string $return_date_to,
    ) {}
}
