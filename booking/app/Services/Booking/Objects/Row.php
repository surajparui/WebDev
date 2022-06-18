<?php

namespace App\Services\Booking\Objects;

use App\Services\Booking\Objects\Seat;

class Row
{
    /**
     * @var int
     */
    public $rowNumber;

    /**
     * @var array
     */
    public $seats = [];

    /**
     * @var null|Aisle
     */
    public $nextRow = null;

    public function __construct(int $rowNumber, array $chairs, int $numberOfRows,  ? array $bookings)
    {
        $this->rowNumber = $rowNumber;
        $this->makeSeats($chairs, $rowNumber, $bookings);
        if ($rowNumber < $numberOfRows) {
            $this->nextRow = new self(++$rowNumber, $chairs, $numberOfRows, $bookings);
        }
    }

    /**
     * @param  array  $chairs
     * @param  int    $rowNumber
     * @param  array  $bookings
     * @return void
     */
    private function makeSeats(array $chairs, int $rowNumber,  ? array $bookings) : void
    {
        foreach ($chairs as $letter => $number) {
            $this->seats[$letter] = new Seat($rowNumber, $letter, $bookings);
        }
    }
}