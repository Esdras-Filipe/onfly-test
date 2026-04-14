<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelOrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_travel_order()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/travel-order', [
                'destination' => 'Lisboa',
                'departure_date' => '2025-06-01',
                'return_date' => '2025-06-10',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Ordem de Viagem criada com Sucesso',
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'Lisboa',
        ]);
    }
}
