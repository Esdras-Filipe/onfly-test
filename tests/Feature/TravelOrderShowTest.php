<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelOrderShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_travel_order()
    {
        $user = User::factory()->create();

        $order = TravelOrderModel::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/travel-order/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }

    public function test_returns_404_when_order_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/travel-order/999');

        $response->assertStatus(404);
    }
}
