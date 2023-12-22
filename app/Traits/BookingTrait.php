<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Requests\BookingRequest;

trait BookingTrait
{
    public function checkAvailability(BookingRequest $request)
    {
        $dates = $this->parseDates($request);
        $result = Booking::getAvailableSpaces($dates['from_date'], $dates['to_date']);

        return response()->json($result);
    }

    public function parseDates(Request $request)
    {
        return [
            'from_date' => Carbon::parse($request->input('from_date')),
            'to_date' => Carbon::parse($request->input('to_date')),
        ];
    }
}
