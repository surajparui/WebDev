<?php

namespace Database\Factories;

use App\Models\Aircraft;
use App\Models\AircraftType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AircraftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Aircraft::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10),
            'aircraft_type_id' => AircraftType::first()->id,
            'sits_count' => 156,
            'rows' => 26,
            'rows_arrangement' => 'A B C _ D E F',
        ];
    }
}
