<?php

namespace App\Domain;

use App\Enums\TravelStatus;

final class TravelOrder
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly int $user_id,
        public readonly string $destination,
        public readonly string $departure_date,
        public readonly string $return_date,
        public readonly TravelStatus $status,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {}
}
