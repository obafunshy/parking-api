<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\PriceType;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{

    use BookingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::available()->paginate();
        return BookingResource::collection($bookings);
    }

    public function store(BookingRequest $request)
    {
        $this->validateAvailability($request);

        $booking = Booking::createBooking(auth()->id(), $request->from_date, $request->to_date);

        return response()->json(['message' => 'Booking created successfully', 'booking' => new BookingResource($booking)], 201);
    }

    public function show(BookingRequest $request)
    {
        $this->validateAvailability($request);

        $bookings = Booking::availableWithinDateRange($request->from_date, $request->to_date)->paginate();

        return BookingResource::collection($bookings);
    }

    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $this->validateAvailability($request);

        $booking->updateBookingDates($request->from_date, $request->to_date);

        return response()->json(['message' => 'Booking updated successfully', 'booking' => new BookingResource($booking)]);
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    private function validateAvailability(BookingRequest $request)
    {
        $response = $this->checkAvailability($request);
        $data = json_decode($response->getContent(), true);

        if (!$data['is_available']) {
            return response()->json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }
    }
}
