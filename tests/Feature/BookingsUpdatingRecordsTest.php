<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingsUpdatingRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_booking()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'from_date' => '2023-12-20',
            'to_date' => '2023-12-26',
            'price' => 100,
        ]);

        Booking::whereBetween('from_date', ['2023-12-11', '2023-12-25'])
            ->orWhereBetween('to_date', ['2023-12-11', '2023-12-25'])
            ->update(['available' => true]);

        $updatedBookingInfo = [
            'from_date' => '2023-12-20',
            'to_date' => '2023-12-26',
        ];

        $response = $this->json('PUT', "/api/booking/{$booking->id}", $updatedBookingInfo);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Booking updated successfully']);
        $response->assertJsonStructure(['message', 'booking']);
    }

    public function test_unauthenticated_user_cannot_update_booking()
    {
        $booking = Booking::factory()->create([
            'from_date' => '2023-12-11',
            'to_date' => '2023-12-25',
            'price' => 100,
        ]);


        $updatedBookingInfo = [
            'from_date' => '2023-12-12',
            'to_date' => '2023-12-26',
        ];

        $response = $this->json('PUT', "/api/booking/{$booking->id}", $updatedBookingInfo);

        $this->assertGuest();
    }

}
