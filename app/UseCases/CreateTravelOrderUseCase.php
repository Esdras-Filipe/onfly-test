<?php

namespace App\UseCases;

use App\Domain\TravelOrder;
use App\Enums\TravelStatus;
use App\DTO\CreateTravelOrderDTO;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateTravelOrderUseCase
{
    public function __construct(public readonly TravelOrderRepositoryInterface $travelOrderRepository) {}

    public function execute(CreateTravelOrderDTO $travelOrderDto): TravelOrder
    {
        $travelOrder = new TravelOrder(
            id: null,
            user_id: auth()->user()->id,
            destination: $travelOrderDto->destination,
            departure_date: $travelOrderDto->departure_date,
            return_date: $travelOrderDto->return_date,
            status: TravelStatus::REQUESTED,
        );

        return $this->travelOrderRepository->save($travelOrder);
    }
}
