<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\PriceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::where("available", true)->paginate();
        return BookingResource::collection($bookings);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {

        $dates = $this->parseDates($request);

        $availabilityResponse = $this->checkAvailability($request);

        $availability = json_decode($availabilityResponse->getContent(), true);

        if (!$availability['overlap']) {
            return response()->json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'from_date' => $dates['from_date'],
            'to_date' =>  $dates['to_date'],
        ]);

        $price = PriceType::calculatePrice($booking->from_date, $booking->to_date);

        $booking->update([
            'price' => $price,
        ]);

        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingRequest $request)
    {

        $availabilityResponse = $this->checkAvailability($request);

        $availability = json_decode($availabilityResponse->getContent(), true);

        if (!$availability['overlap']) {
            return response()->json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }

        $bookings = Booking::availableWithinDateRange($request->from_date, $request->to_date)->paginate();

        return BookingResource::collection($bookings);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $dates = $this->parseDates($request);

        $availabilityResponse = $this->checkAvailability($request);

        $availability = json_decode($availabilityResponse->getContent(), true);

        if (!$availability['overlap']) {
            return response()->json(['error' => 'Parking spaces not available for the specified date range'], 422);
        }

        $booking->update([
            'from_date' => $dates['from_date'],
            'to_date' => $dates['to_date'],
        ]);

        $price = PriceType::calculatePrice($booking->from_date, $booking->to_date);
        $booking->update(['price' => $price]);

        return response()->json(['message' => 'Booking updated successfully', 'booking' => new BookingResource($booking)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:now',
            'to_date' => 'required|date|after:from_date',
        ]);

        $dates = $this->parseDates($request);

        $result = Booking::hasOverlapAndAvailableSpaces($dates['from_date'], $dates['to_date']);
        return response()->json($result);
    }

    private function parseDates(Request $request)
    {
        return [
            'from_date' => Carbon::parse($request->input('from_date')),
            'to_date' => Carbon::parse($request->input('to_date')),
        ];
    }
}
