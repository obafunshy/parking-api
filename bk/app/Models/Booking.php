<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'from_date', 'to_date', 'price_type_id', 'price', 'available'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceType()
    {
        return $this->belongsTo(PriceType::class);
    }

    public static function hasOverlapAndAvailableSpaces($from_date, $to_date)
{
    $totalSpaces = 10;

    $overlap = static::where(function ($query) use ($from_date, $to_date) {
        $query->where(function ($q) use ($from_date, $to_date) {
            $q->where('from_date', '>=', $from_date)
                ->where('from_date', '<', $to_date)
                ->where('available', true);
        })->orWhere(function ($q) use ($from_date, $to_date) {
            $q->where('to_date', '>', $from_date)
                ->where('to_date', '<=', $to_date)
                ->where('available', true);
        });
    })->exists();

    // Count unique days within the date range
    $occupiedDays = static::where('from_date', '<', $to_date)
        ->where('to_date', '>', $from_date)
        ->where('available', true)
        ->groupBy('created_at')
        ->count();

    // Calculate available spaces
    $availableSpaces = max(0, $totalSpaces - $occupiedDays);

    $overlap = ($availableSpaces === $totalSpaces);

    return [
        'overlap' => $overlap,
        'available_spaces' => $availableSpaces,
    ];
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
}
