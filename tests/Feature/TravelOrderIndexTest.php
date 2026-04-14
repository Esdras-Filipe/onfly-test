<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelOrderIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_travel_orders()
    {
        $user = User::factory()->create();

        TravelOrderModel::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/travel-order');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
                'meta' => ['total', 'perPage', 'currentPage']
            ]);
    }
}
