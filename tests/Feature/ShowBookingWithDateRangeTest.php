<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowBookingWithDateRangeTest extends TestCase
{
   use RefreshDatabase;

   public function test_show_bookings_within_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $bookingFromDate = '2023-12-15';
        $bookingToDate = '2023-12-20';

        Booking::factory()->create([
            'from_date' => $bookingFromDate,
            'to_date' => $bookingToDate,
            'available' => true,
        ]);

        $response = $this->json('GET', "/api/booking", [
            'from_date' => $bookingFromDate,
            'to_date' => $bookingToDate,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user',
                    'from_date',
                    'to_date',
                    'price',
                ],
            ],
        ]);

        $jsonContent = $response->json();
        $this->assertTrue(is_array($jsonContent['data']));

        collect($jsonContent['data'])->each(function ($booking) use ($bookingFromDate, $bookingToDate) {
            $bookingFromDate = Carbon::parse($booking['from_date'])->toDateString();
            $bookingToDate = Carbon::parse($booking['to_date'])->toDateString();

            $this->assertTrue($bookingFromDate >= $bookingFromDate && $bookingToDate <= $bookingToDate);
        });
    }
}
