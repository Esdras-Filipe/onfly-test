<?php

namespace App\DTO;

final class TravelOrderCollectionDTO
{
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage
    ) {}
}
