<?php

namespace App\UseCases;

use App\Domain\Exceptions\TravelOrderApprovedCanceledException;
use App\Domain\Exceptions\TravelOrderNotFoundException;
use App\Domain\Exceptions\TravelOrderUnauthorizedActionException;
use App\Domain\TravelOrder;
use App\Enums\TravelStatus;
use App\DTO\UpdateTravelOrderStatusDTO;
use App\Models\User;
use App\Notifications\TravelOrderStatusChanged;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;

class UpdateTravelOrderStatusUseCase
{
    public function __construct(public readonly TravelOrderRepositoryInterface $travelOrderRepository) {}

    public function execute(UpdateTravelOrderStatusDTO $travelOrderDto): TravelOrder
    {
        if (!auth()->user()->isAdmin())
            throw new TravelOrderUnauthorizedActionException();

        $travelOrder = $this->travelOrderRepository->findById($travelOrderDto->id);
        if (is_null($travelOrder))
            throw new TravelOrderNotFoundException();

        if (in_array($travelOrder->status, [TravelStatus::APPROVED, TravelStatus::CANCELED]))
            throw new TravelOrderApprovedCanceledException();

        $travelOrderUpdate = new TravelOrder(
            id: $travelOrder->id,
            user_id: $travelOrder->user_id,
            destination: $travelOrder->destination,
            departure_date: $travelOrder->departure_date,
            return_date: $travelOrder->return_date,
            status: TravelStatus::from($travelOrderDto->status),
            created_at: $travelOrder->created_at,
            updated_at: $travelOrder->updated_at
        );

        $this->travelOrderRepository->update($travelOrderUpdate);
        $user = $this->travelOrderRepository->findUserById($travelOrderUpdate->user_id);
        $user->notify(new TravelOrderStatusChanged($travelOrderUpdate));

        return $travelOrderUpdate;
    }

    public function findUserById(int $userId): User
    {
        return User::findOrFail($userId);
    }
}
