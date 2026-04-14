<?php

namespace Tests\Unit\UseCases;

use App\Domain\TravelOrder;
use App\DTO\CreateTravelOrderDTO;
use App\Enums\TravelStatus;
use App\Models\User;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use App\UseCases\CreateTravelOrderUseCase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Faker\Factory as Faker;

class CreateTravelOrderUseCaseTest extends TestCase
{
    private TravelOrderRepositoryInterface $repository;
    private $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TravelOrderRepositoryInterface::class);
        $this->actingAsUser();
        $this->useCase = new CreateTravelOrderUseCase($this->repository);
    }

    /**
     * Limpar os possíveis caches para evitar passar configuração de um teste para o outro
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeTravelOrder(array $overrides = []): TravelOrder
    {
        $faker = Faker::create();

        return new TravelOrder(
            id:             $overrides['id']             ?? 1,
            user_id:        $overrides['user_id']        ?? 1,
            destination:    $overrides['destination']    ?? $faker->lexify('??????'),
            departure_date: $overrides['departure_date'] ?? '2025-01-01',
            return_date:    $overrides['return_date']    ?? '2026-01-01',
            status:         $overrides['status']         ?? TravelStatus::REQUESTED,
            created_at:     $overrides['created_at']     ?? now()->toDateTimeString(),
            updated_at:     $overrides['updated_at']     ?? now()->toDateTimeString(),
        );
    }

    private function actingAsUser(bool $isAdmin = false)
    {
        $user        = Mockery::mock(User::class)->makePartial();
        $user->id    = 1;
        $user->name  = 'Test User';
        $user->email = 'test@test.com';

        $user->shouldReceive('isAdmin')->andReturn($isAdmin);
        $user->shouldReceive('notify')->andReturn(null);

        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('check')->andReturn(true);
    }

    /**
     * Passando parametros validos para validar se criou com sucesso a ordem de viagem
     */
    public function test_create_travel_order_successfully(): void
    {
        $dto = new CreateTravelOrderDTO(
            destination: 'Lisboa',
            departure_date: '2025-06-01',
            return_date: '2025-06-10'
        );

        $expectedOrder = $this->makeTravelOrder(['destination' => 'Lisboa']);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->withArgs(function (TravelOrder $order) {
                return $order->destination    === 'Lisboa'
                    && $order->departure_date === '2025-06-01'
                    && $order->return_date    === '2025-06-10'
                    && $order->status         === TravelStatus::REQUESTED
                    && $order->id             === null;
            })
            ->andReturn($expectedOrder);

        $result = $this->useCase->execute($dto);

        $this->assertInstanceOf(TravelOrder::class, $result);
        $this->assertEquals('Lisboa', $result->destination);
        $this->assertEquals(TravelStatus::REQUESTED, $result->status);
    }

    /**
     * Verificar se ao criar a ordem de viagem o status é sempre retornado como requested
     */
    public function test_create_travel_order_sets_status_as_requested(): void
    {
        $dto = new CreateTravelOrderDTO(
            destination: 'Paris',
            departure_date: '2025-07-01',
            return_date: '2025-07-15'
        );

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->withArgs(fn(TravelOrder $o) => $o->status === TravelStatus::REQUESTED)
            ->andReturn($this->makeTravelOrder(['status' => TravelStatus::REQUESTED]));

        $result = $this->useCase->execute($dto);
        $this->assertEquals(TravelStatus::REQUESTED, $result->status);
    }
}
