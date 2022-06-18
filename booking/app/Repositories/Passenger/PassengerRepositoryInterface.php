<?php

namespace App\Repositories\Passenger;

interface PassengerRepositoryInterface
{
    public function getWithBookings(): array;

    public function deleteAll(): void;
}
