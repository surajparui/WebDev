<?php

namespace App\Repositories\Aircraft;

interface AircraftRepositoryInterface
{
    public function getWithBookings(int $id): array;
}
