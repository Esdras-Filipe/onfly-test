<?php

namespace Tests\Unit\UseCases;

use App\Domain\TravelOrder;
use App\Domain\Exceptions\TravelOrderNotFoundException;
use App\Domain\Exceptions\TravelOrderApprovedCanceledException;
use App\Domain\Exceptions\TravelOrderUnauthorizedActionException;
use App\DTO\UpdateTravelOrderStatusDTO;
use App\Enums\TravelStatus;
use App\Models\User;
use App\Notifications\TravelOrderStatusChanged;
use App\Repositories\Contracts\TravelOrderRepositoryInterface;
use App\UseCases\UpdateTravelOrderStatusUseCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class UpdateTravelOrderUseCaseTest extends TestCase
{
    private TravelOrderRepositoryInterface $repository;
    private $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TravelOrderRepositoryInterface::class);
        $this->useCase    = new UpdateTravelOrderStatusUseCase($this->repository);
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
     * Verificar se permissão do usuário admin de alterar ordem de viagem está correta
     */
    public function test_admin_can_update_travel_order_status(): void
    {
        Notification::fake();
        $this->actingAsUser(isAdmin: true);

        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('notify')->once()->with(Mockery::type(TravelOrderStatusChanged::class));

        $order = $this->makeTravelOrder(['status' => TravelStatus::REQUESTED]);

        $this->repository->shouldReceive('findById')->once()->with(1)->andReturn($order);
        $this->repository->shouldReceive('update')->once()->andReturn($order);
        $this->repository->shouldReceive('findUserById')->once()->with($order->user_id)->andReturn($mockUser);

        $dto    = new UpdateTravelOrderStatusDTO(id: 1, status: TravelStatus::APPROVED->value);
        $result = $this->useCase->execute($dto);

        $this->assertEquals(TravelStatus::APPROVED, $result->status);
    }

    /**
     * Verificar se está sendo bloqueado de um usuário comum atualizar uma ordem de viagem
     */
    public function test_common_user_cannot_update_travel_order_status(): void
    {
        $this->actingAsUser(isAdmin: false);
        $this->expectException(TravelOrderUnauthorizedActionException::class);

        $dto = new UpdateTravelOrderStatusDTO(id: 1, status: TravelStatus::APPROVED->value);
        $this->useCase->execute($dto);
    }

    /**
     * Verificar se a regra de negócio de impedir uma ordem de viagem ser alterada depois de ser sido aprovada está correta
     */
    public function test_cannot_update_status_of_approved_order(): void
    {
        $this->actingAsUser(isAdmin: true);
        $order = $this->makeTravelOrder(['status' => TravelStatus::APPROVED]);

        $this->repository->shouldReceive('findById')->once()->andReturn($order);
        $this->expectException(TravelOrderApprovedCanceledException::class);

        $dto = new UpdateTravelOrderStatusDTO(id: 1, status: TravelStatus::CANCELED->value);
        $this->useCase->execute($dto);
    }

    /**
     * Verificar se a regra de negócio de impedir uma ordem de viagem ser alterada depois de ser sido cancelada está correta
     */
    public function test_cannot_update_status_of_canceled_order(): void
    {
        $this->actingAsUser(isAdmin: true);
        $order = $this->makeTravelOrder(['status' => TravelStatus::CANCELED]);

        $this->repository->shouldReceive('findById')->once()->andReturn($order);
        $this->expectException(TravelOrderApprovedCanceledException::class);

        $dto = new UpdateTravelOrderStatusDTO(id: 1, status: TravelStatus::APPROVED->value);
        $this->useCase->execute($dto);
    }

    /**
     * Verificar se o sistema está validando código de ordem de viagem inexistente
     */
    public function test_update_throws_not_found_when_order_does_not_exist(): void
    {
        $this->actingAsUser(isAdmin: true);
        $this->repository->shouldReceive('findById')->once()->andReturn(null);

        $this->expectException(TravelOrderNotFoundException::class);

        $dto = new UpdateTravelOrderStatusDTO(id: 999, status: TravelStatus::APPROVED->value);
        $this->useCase->execute($dto);
    }

    /**
     * Verificar se o sistema está disparando a notificação para o usuário ao alterar o status da ordem de viagem
     */
    public function test_notification_is_sent_after_status_update(): void
    {
        $this->actingAsUser(isAdmin: true);

        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('notify')->once()->with(Mockery::type(TravelOrderStatusChanged::class));

        $order = $this->makeTravelOrder(['status' => TravelStatus::REQUESTED]);

        $this->repository->shouldReceive('findById')->once()->andReturn($order);
        $this->repository->shouldReceive('update')->once()->andReturn($order);
        $this->repository->shouldReceive('findUserById')->once()->with($order->user_id)->andReturn($mockUser);

        $dto = new UpdateTravelOrderStatusDTO(id: 1, status: TravelStatus::APPROVED->value);
        $this->useCase->execute($dto);
    }
}
