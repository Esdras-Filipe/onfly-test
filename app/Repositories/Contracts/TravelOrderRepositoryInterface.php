<?php

namespace App\Repositories\Contracts;

use App\Domain\TravelOrder;
use App\DTO\ListTravelOrderDTO;
use App\Models\User;
use Illuminate\Support\Collection;

interface TravelOrderRepositoryInterface
{
    public function save(TravelOrder $order): TravelOrder;
    public function findById(int $id): ?TravelOrder;
    public function findByIdAndUser(int $id, int $userId): ?TravelOrder;
    public function search(ListTravelOrderDTO $travelOrder);
    public function searchWithUserId(ListTravelOrderDTO $travelOrder, int $user_id);
    public function update(TravelOrder $travelOrder): TravelOrder;
    public function findUserById(int $userId): User;
}
