<?php

namespace App\Services\Booking\Objects;

class Seat
{
    /**
     * Row number
     * @var int
     */
    public $rowNumber;

    /**
     * Seat letter
     * @var string
     */
    public $letter;

    /**
     * If the seat is occupied
     * @var bool
     */
    public $occupied;

    /**
     * If the seat is on the window
     * @var bool
     */
    public $isWindow;

    /**
     * @var array
     */
    private $windows = ['A', 'F'];

    public function __construct(int $rowNumber, string $letter,  ? array $bookings)
    {
        $this->rowNumber = $rowNumber;
        $this->letter    = $letter;
        $this->isWindow  = in_array($letter, $this->windows) ? true : false;
        $this->occupied  = $this->isBooked($bookings, $rowNumber, $letter);
    }

    /**
     * Verify
     * @param  array   $bookings
     * @param  int     $rowNumber
     * @param  string  $letter
     * @return boolean
     */
    private function isBooked( ? array $bookings, int $rowNumber, string $letter) : bool
    {
        if (!$bookings) {
            return false;
        }

        foreach ($bookings as $booking) {
            if ($booking['row_number'] == $rowNumber && $booking['row_seat'] == $letter) {
                return true;
            }
        }
        return false;
    }
}
