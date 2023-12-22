<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookings_list_returns_paginated_data_correctly() {
        Booking::factory(16)->create();
        $response = $this->getJson('/api/bookings');
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.current_page', 1);
    }

    public function test_bookings_list_only_show_available_dates() {
        $user1 = User::factory()->create(['name' => 'Smith']);
        $booking_available = Booking::factory()->create(['available'=> 1, 'user_id' => $user1->id]);

        $user2 = User::factory()->create(['name' => 'Mike']);
        $booking_unavailable = Booking::factory()->create(['available' => 0, 'user_id' => $user2->id]);

        $response = $this->getJson('/api/bookings');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.user', $booking_available->user->name);
    }
}
