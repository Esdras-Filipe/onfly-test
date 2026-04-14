<?php

namespace Tests\Unit\UseCases;

use App\Domain\TravelOrder;
use App\DTO\ListTravelOrderDTO;
use App\DTO\TravelOrderCollectionDTO;
use App\Enums\TravelStatus;
use App\Models\User;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use App\UseCases\ListTravelOrderUseCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ListTravelOrderUseCaseTest extends TestCase
{
    private TravelOrderRepositoryInterface $repository;
    private $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TravelOrderRepositoryInterface::class);
        $this->useCase = new ListTravelOrderUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeTravelOrder(array $overrides = []): TravelOrder
    {
        return new TravelOrder(
            id: $overrides['id']             ?? 1,
            user_id: $overrides['user_id']        ?? 1,
            requester_name: $overrides['requester_name'] ?? 'Usuário Comum',
            destination: $overrides['destination']    ?? 'Lisboa',
            departure_date: $overrides['departure_date'] ?? '2025-06-01',
            return_date: $overrides['return_date']    ?? '2025-06-10',
            status: $overrides['status']         ?? TravelStatus::REQUESTED,
            created_at: $overrides['created_at']     ?? now()->toDateTimeString(),
            updated_at: $overrides['updated_at']     ?? now()->toDateTimeString(),
        );
    }

    private function actingAsUser(bool $isAdmin = false): User
    {
        $user           = Mockery::mock(User::class)->makePartial();
        $user->id       = 1;
        $user->name     = 'Test User';
        $user->email    = 'test@test.com';
        $user->shouldReceive('isAdmin')->andReturn($isAdmin);
        $user->shouldReceive('notify')->andReturn(null);

        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('check')->andReturn(true);

        return $user;
    }

    /**
     * Verificar se o usuário Admin está com a permissão correta para listar todos as ordens de viagem
     */
    public function test_admin_can_list_all_travel_orders(): void
    {
        $this->actingAsUser(isAdmin: true);

        $orders    = [$this->makeTravelOrder(['user_id' => 1]), $this->makeTravelOrder(['user_id' => 2])];
        $paginator = new LengthAwarePaginator($orders, 2, 15, 1);

        $this->repository->shouldReceive('search')->once()->andReturn($paginator);

        $dto = new ListTravelOrderDTO(
            destination: null,
            status: null,
            sortBy: null,
            sortDirection: null,
            perPage: 15,
            page: 1,
            departure_date_from: null,
            departure_date_to: null,
            return_date_from: null,
            return_date_to: null,
        );

        $result = $this->useCase->execute($dto);

        $this->assertInstanceOf(TravelOrderCollectionDTO::class, $result);
        $this->assertCount(2, $result->items);
        $this->assertEquals(2, $result->total);
    }

    /**
     * Verificar se o usuário comum está listando somente as suas ordens de viagem
     */
    public function test_common_user_only_lists_own_travel_orders(): void
    {
        $this->actingAsUser(isAdmin: false);

        $orders    = [$this->makeTravelOrder(['user_id' => 1])];
        $paginator = new LengthAwarePaginator($orders, 1, 15, 1);

        $this->repository
            ->shouldReceive('searchWithUserId')
            ->once()
            ->with(Mockery::type(ListTravelOrderDTO::class), 1)
            ->andReturn($paginator);

        $this->repository->shouldNotReceive('search');

        $dto = new ListTravelOrderDTO(
            destination: null,
            status: null,
            sortBy: null,
            sortDirection: null,
            perPage: 15,
            page: 1,
            departure_date_from: null,
            departure_date_to: null,
            return_date_from: null,
            return_date_to: null,
        );

        $result  = $this->useCase->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals(1, $result->items[0]->user_id);
    }

    /**
     * Verificando se o filtro de destino está funcionando corretamente, passando um destino inexistente
     */
    public function test_list_returns_empty_collection_when_no_orders_found(): void
    {
        $this->actingAsUser(isAdmin: true);

        $paginator = new LengthAwarePaginator([], 0, 15, 1);

        $this->repository->shouldReceive('search')->once()->andReturn($paginator);

        $dto = new ListTravelOrderDTO(
            destination: 'Destino Inexistente',
            status: null,
            sortBy: null,
            sortDirection: null,
            perPage: 15,
            page: 1,
            departure_date_from: null,
            departure_date_to: null,
            return_date_from: null,
            return_date_to: null,
        );

        $result = $this->useCase->execute($dto);

        $this->assertCount(0, $result->items);
        $this->assertEquals(0, $result->total);
    }
}
