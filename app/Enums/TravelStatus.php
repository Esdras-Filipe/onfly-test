<?php

namespace App\Enums;

enum TravelStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
}
