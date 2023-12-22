<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingsDeletingRecordsTest extends TestCase
{
   use RefreshDatabase;

   public function test_destroying_booking()
    {
        $booking = Booking::factory()->create();
        $response = $this->json('DELETE', "/api/booking/{$booking->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Booking deleted successfully']);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }
    public function test_destroying_nonexistent_booking()
    {
        $response = $this->json('DELETE', "/api/booking/999");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No query results for model [App\\Models\\Booking] 999']);
    }
}
