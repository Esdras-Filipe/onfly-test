<?php

namespace App\UseCases;

use App\DTO\ListTravelOrderDTO;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use App\DTO\TravelOrderCollectionDTO;

class ListTravelOrderUseCase
{
    public function __construct(public readonly TravelOrderRepositoryInterface $travelOrderRepository) {}

    public function execute(ListTravelOrderDTO $travelOrderDto): TravelOrderCollectionDTO
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $travelOrders = $this->travelOrderRepository->search($travelOrderDto);
        } else {
            $travelOrders = $this->travelOrderRepository->searchWithUserId($travelOrderDto, $user->id);
        }

        return new TravelOrderCollectionDTO(
            items: $travelOrders->items(),
            total: $travelOrders->total(),
            perPage: $travelOrders->perPage(),
            currentPage: $travelOrders->currentPage()
        );
    }
}
