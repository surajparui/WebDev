<?php

namespace App\Services\Booking;

use App\Services\Booking\Objects\Bookings;

class BookingService
{

    /**
     * @param  array  $aircraft
     * @param  array  $passengers
     * @return array
     */
    public function getSeatsAvailable(array $aircraft, array $passengers): array
    {
        $bookings = new Bookings($aircraft);

        $bookingsToDo = [];
        foreach ($passengers as $passenger) {

            $seatsAvailable = $bookings->book($passenger['quantity_seats']);

            array_push($bookingsToDo, $this->markSeats($seatsAvailable, $passenger, $aircraft['id']));
        }

        return $bookingsToDo;
    }

    /**
     * @param  array  $seatsAvailable
     * @param  array  $passenger
     * @param  int    $aircraftId
     * @return array
     */
    private function markSeats(array $seatsAvailable, array $passenger, int $aircraftId): array
    {
        $seats = [];
        foreach ($seatsAvailable as $seat) {
            array_push($seats, [
                'aircraft_id' => $aircraftId,
                'row_number'  => $seat->rowNumber,
                'row_seat'    => $seat->letter,
            ]);
        }

        return [
            "passenger" => $passenger['name'],
            "seats"     => $seats,
        ];
    }

    /**
     * Using the database response to make the api response
     * @param  array  $passengers
     * @return array
     */
    public function makeResponse(array $passengers): array
    {
        $response = [];
        foreach ($passengers as $passenger) {
            array_push($response, [
                'passenger' => $passenger['name'],
                'seats'     => join(", ",
                    array_map(function ($item) {
                        return $item['row_seat'] . $item['row_number'];
                    }, $passenger['bookings'])
                ),
            ]);
        }
        return $response;
    }

    /**
     * @param  array  $aircraft
     * @param  array  $passengers
     * @return void
     */
    public function validateSeatsAvailable(array $aircraft, array $passengers): void
    {
        if (count($aircraft['bookings']) == $aircraft['sits_count']) {
            throw new \Exception("No seats available");
        }

        $total = array_reduce($passengers, function ($sum, $item) {
            return $sum + $item['quantity_seats'];
        }, 0);

        if (($total + count($aircraft['bookings'])) > $aircraft['sits_count']) {
            throw new \Exception("There are no enough seats for this booking");
        }
    }
}
