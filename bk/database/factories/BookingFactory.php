<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\PriceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $priceData = PriceType::calculatePriceBasedOnRandomDates();

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'from_date' => $priceData['from_date'],
            'to_date' => $priceData['to_date'],
            'price_type_id' => null,
            'price' => $priceData['price'],
            'available' => true,
        ];
    }
}
