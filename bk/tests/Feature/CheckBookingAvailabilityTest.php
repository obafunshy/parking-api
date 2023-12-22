<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckBookingAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_availability_returns_correct_response()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $from_date = Carbon::parse('2023-12-15')->toDateString();
        $to_date =   Carbon::parse('2023-12-20')->toDateString();

        Booking::factory(5)->create([
            'from_date'=> $from_date,
            'to_date'=> $to_date
        ]); // assume 5 available bookings

        Booking::factory(5)->create([
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'available' => 0,
        ]); // assume 5 unavailable bookings

        $response = $this->json('GET', "/api/availability?from_date=$from_date&to_date=$to_date");

        $response->assertStatus(200);
        $response->assertJsonStructure(['overlap', 'available_spaces']);
        $response->assertJson([
            'overlap' => false,
            'available_spaces' => 5, // Assuming you have a total of 10 spaces
        ]);

    }
}
