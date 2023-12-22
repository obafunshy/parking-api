<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingsStoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_create_booking() {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Booking::factory(10)->create();

        Booking::whereBetween('from_date', ['2023-12-11', '2023-12-25'])
           ->orWhereBetween('to_date', ['2023-12-11', '2023-12-25'])
           ->update(['available' => true]);

        $booking_info = [
            'user_id'=> $user->id,
            "from_date" => '2023-12-11',
            "to_date" => '2023-12-25',
            "price" => 100,
        ];

        $response = $this->json('POST', '/api/booking', $booking_info);
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Booking created successfully']);
        $response->assertJsonStructure(['message', 'booking']);
    }

    public function test_authenticated_user_before_creating_booking() {

        Booking::factory()->create();

        $booking_info = [
            "from_date" => '2023-12-11',
            "to_date" => '2023-12-28',
            "price" => 100,
        ];

        $response = $this->json('POST', '/api/booking', $booking_info);
        $this->assertGuest();
    }


}
