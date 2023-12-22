<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'from_date', 'to_date', 'price', 'available'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceType()
    {
        return $this->belongsTo(PriceType::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    public static function getAvailableSpaces($from_date, $to_date)
    {
        $totalSpaces = config('constants.TOTAL_PARKING');

        $occupiedDays = static::where('from_date', '<', $to_date)
            ->where('to_date', '>', $from_date)
            ->where('available', false)
            ->groupByRaw('DATE(from_date)')
            ->count();

        $availableSpaces = max(0, $totalSpaces - $occupiedDays);

        return [
            'is_available' => $availableSpaces > 0,
            'available_spaces' => $availableSpaces,
        ];
    }

    public static function calculatePrice($from_date, $to_date)
    {
        return PriceType::calculatePrice($from_date, $to_date);
    }

    public function scopeAvailableWithinDateRange($query, $from_date, $to_date)
    {
        return $query->where('available', true)
            ->where(function ($query) use ($from_date, $to_date) {
                $query->whereBetween('from_date', [$from_date, $to_date])
                    ->orWhereBetween('to_date', [$from_date, $to_date])
                    ->orWhere(function ($query) use ($from_date, $to_date) {
                        $query->where('from_date', '<', $from_date)
                            ->where('to_date', '>', $to_date);
                    });
            });
    }

    public static function createBooking($userId, $fromDate, $toDate)
    {
        $booking = static::create([
            'user_id' => $userId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        $price = static::calculatePrice($booking->from_date, $booking->to_date);

        $booking->update(['price' => $price]);

        return $booking;
    }

    public function updateBookingDates($fromDate, $toDate)
    {
        $this->update([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        $price = static::calculatePrice($this->from_date, $this->to_date);

        $this->update(['price' => $price]);
    }


}


