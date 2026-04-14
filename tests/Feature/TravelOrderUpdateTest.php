<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelOrderUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_travel_order_status()
    {
        $admin = User::factory()->create([
            'is_admin' => true
        ]);

        $order = TravelOrderModel::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->patchJson("/api/travel-order/{$order->id}", [
                'status' => 'approved'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Ordem de Viagem Atualizada com Sucesso'
            ]);
    }

    public function test_common_user_cannot_update_status()
    {
        $user = User::factory()->create([
            'is_admin' => false
        ]);

        $order = TravelOrderModel::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/travel-order/{$order->id}", [
                'status' => 'approved'
            ]);

        $response->assertStatus(403);
    }
}
