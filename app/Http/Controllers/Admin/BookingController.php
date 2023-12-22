<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Response;

class BookingController extends Controller
{

    use BookingTrait;
    public function store(BookingRequest $request)
    {
        $dates = $this->parseDates($request);

        $response = $this->checkAvailability($dates);

        if (!$response['is_available']) {
            return Response::json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }

        $booking = Booking::createBooking(Auth::id(), $dates['from_date'], $dates['to_date']);

        return new BookingResource($booking);
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $dates = $this->parseDates($request);

        $response = $this->checkAvailability($dates);

        if (!$response['is_available']) {
            return Response::json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }

        $booking->updateBookingDates($dates['from_date'], $dates['to_date']);

        return Response::json(['message' => 'Booking updated successfully', 'booking' => $booking]);
    }
}
