<?php

namespace App\UseCases;

use App\Domain\Exceptions\TravelOrderNotFoundException;
use App\Domain\TravelOrder;
use App\DTO\ShowTravelOrderDTO;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;

class ShowTravelOrderUseCase
{
    public function __construct(public readonly TravelOrderRepositoryInterface $travelOrderRepository) {}

    public function execute(ShowTravelOrderDTO $travelOrderDto): TravelOrder
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $travelOrder = $this->travelOrderRepository->findById($travelOrderDto->id);
        } else {
            $travelOrder = $this->travelOrderRepository->findByIdAndUser($travelOrderDto->id, $user->id);
        }

        if (is_null($travelOrder))
            throw new TravelOrderNotFoundException();

        return $travelOrder;
    }
}
