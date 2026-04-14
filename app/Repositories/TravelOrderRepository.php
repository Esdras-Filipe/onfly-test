<?php

namespace App\Repositories;

use App\Domain\TravelOrder;
use App\Enums\TravelStatus;
use App\Models\TravelOrderModel;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use App\DTO\ListTravelOrderDTO;

class TravelOrderRepository implements TravelOrderRepositoryInterface
{
    public function findById(int $id): ?TravelOrder
    {
        $model = TravelOrderModel::where('id', $id)->first();
        if (!$model)
            return null;

        return $this->createEntity($model);
    }

    public function findByIdAndUser(int $id, int $userId): ?TravelOrder
    {
        $model = TravelOrderModel::where('id', $id)->where('user_id', $userId)->first();

        if (!$model)
            return null;

        return $this->createEntity($model);
    }

    public function save(TravelOrder $order): TravelOrder
    {
        $orderCreated = TravelOrderModel::create([
            'user_id'        => $order->user_id,
            'destination'    => $order->destination,
            'departure_date' => $order->departure_date,
            'return_date'    => $order->return_date,
            'status'         => $order->status->value
        ]);

        return new TravelOrder(
            id: $orderCreated->id,
            user_id: $orderCreated->user_id,
            destination: $orderCreated->destination,
            departure_date: $orderCreated->departure_date,
            return_date: $orderCreated->return_date,
            status: TravelStatus::from($orderCreated->status),
        );
    }

    public function update(TravelOrder $order): TravelOrder
    {
        TravelOrderModel::where('id', $order->id)->update([
            'user_id'        => $order->user_id,
            'destination'    => $order->destination,
            'departure_date' => $order->departure_date,
            'return_date'    => $order->return_date,
            'status'         => $order->status->value
        ]);

        return $order;
    }

    private function createEntity(object $model): TravelOrder
    {
        return new TravelOrder(
            id: $model->id,
            user_id: $model->user_id,
            destination: $model->destination,
            departure_date: $model->departure_date,
            return_date: $model->return_date,
            status: TravelStatus::from($model->status),
            created_at: $model->created_at ?? null,
            updated_at: $model->updated_at ?? null
        );
    }

    public function search(ListTravelOrderDTO $travelOrder)
    {
        $query = TravelOrderModel::query();

        if ($travelOrder->departure_date_from) {
            $query->where('departure_date', '>=', $travelOrder->departure_date_from);
        }

        if ($travelOrder->departure_date_to) {
            $query->where('departure_date', '<=', $travelOrder->departure_date_to);
        }

        if ($travelOrder->return_date_from) {
            $query->where('return_date', '>=', $travelOrder->return_date_from);
        }

        if ($travelOrder->return_date_to) {
            $query->where('return_date', '<=', $travelOrder->return_date_to);
        }

        if ($travelOrder->destination) {
            $query->where('destination', 'LIKE', $travelOrder->destination);
        }

        if ($travelOrder->status) {
            $query->where('status', 'LIKE', $travelOrder->status);
        }

        if ($travelOrder->sortBy)
            $query->orderBy($travelOrder->sortBy, $travelOrder->sortDirection ?? 'asc');

        $paginator = $query->paginate(
            perPage: $dto->perPage ?? 15,
            page: $dto->page ?? 1
        );

        $paginator->getCollection()->transform(function (TravelOrderModel $model) {
            return $this->createEntity($model);
        });

        return $paginator;
    }

    public function searchWithUserId(ListTravelOrderDTO $travelOrder, int $user_id)
    {
        $query = TravelOrderModel::query();

        if ($travelOrder->departure_date_from) {
            $query->where('departure_date', '>=', $travelOrder->departure_date_from);
        }

        if ($travelOrder->departure_date_to) {
            $query->where('departure_date', '<=', $travelOrder->departure_date_to);
        }

        if ($travelOrder->return_date_from) {
            $query->where('return_date', '>=', $travelOrder->return_date_from);
        }

        if ($travelOrder->return_date_to) {
            $query->where('return_date', '<=', $travelOrder->return_date_to);
        }

        if ($travelOrder->destination) {
            $query->where('destination', 'LIKE', $travelOrder->destination);
        }

        if ($travelOrder->status) {
            $query->where('status', 'LIKE', $travelOrder->status);
        }

        $query->where('user_id', $user_id);

        if ($travelOrder->sortBy)
            $query->orderBy($travelOrder->sortBy, $travelOrder->sortDirection ?? 'asc');

        $paginator = $query->paginate(
            perPage: $dto->perPage ?? 15,
            page: $dto->page ?? 1
        );

        $paginator->getCollection()->transform(function (TravelOrderModel $model) {
            return $this->createEntity($model);
        });

        return $paginator;
    }
}
