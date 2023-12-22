<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekday_price',
        'weekend_price',
        'summer_price',
        'winter_price',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public static function calculatePrice($from_date, $to_date, $weekdayPrice, $weekendPrice, $summerPrice, $winterPrice)
    {
        $start = Carbon::parse($from_date);
        $end = Carbon::parse($to_date);

        $totalPrice = 0;

        while ($start->lte($end)) {
            $totalPrice += self::calculateDailyPrice($start, $weekdayPrice, $weekendPrice, $summerPrice, $winterPrice);
            $start->addDay();
        }

        return $totalPrice;
    }

    private static function calculateDailyPrice($date, $weekdayPrice, $weekendPrice, $summerPrice, $winterPrice)
    {
        $isWeekend = $date->isWeekend();
        $isSummer = self::isSummer($date);
        $isWinter = self::isWinter($date);

        if ($isWinter) {
            return $winterPrice;
        }

        return $isWeekend ? $weekendPrice : ($isSummer ? $summerPrice : $weekdayPrice);
    }

    protected static function isSummer($date)
    {
        $summerStart = Carbon::create(null, 6, 1, 0, 0, 0); // June 1st
        $summerEnd = Carbon::create(null, 8, 31, 23, 59, 59); // August 31st

        return $date->between($summerStart, $summerEnd);
    }

    public static function isWinter($date)
    {
        $winterStart = Carbon::create(null, 12, 1, 0, 0, 0); // December 1st
        $winterEnd = Carbon::create(null, 2, 28, 23, 59, 59); // February 28th

        // Adjust winter end date for leap years
        if ($date->isLeapYear()) {
            $winterEnd->day(29);
        }

        return $date->between($winterStart, $winterEnd);
    }

    public static function getPriceTypeByDateRange($from_date, $to_date)
    {
       return self::where('from_date', '<=', $from_date)
            ->where('to_date', '>=', $to_date)
            ->first();
    }

    // to generate factory dates without querying the database
    public static function calculatePriceBasedOnRandomDates(): array
    {
        $from_date = Carbon::now()->addDays(rand(1, 30));
        $to_date = Carbon::instance($from_date)->addDays(rand(1, 30));

        $priceType = PriceType::inRandomOrder()->first();

        if ($priceType) {
            $weekday_price = $priceType->weekday_price;
            $weekend_price = $priceType->weekend_price;
            $summer_price =  $priceType->summer_price;
            $winter_price =  $priceType->winter_price;

            $price = self::calculatePrice($from_date, $to_date, $weekday_price, $weekend_price, $summer_price, $winter_price);

            return [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'price' => $price,
            ];
        }

        return [
            'from_date' => null,
            'to_date' => null,
            'price' => null,
        ];
    }
}
