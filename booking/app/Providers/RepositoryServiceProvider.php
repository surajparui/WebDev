<?php

namespace App\Providers;

use App\Repositories\Aircraft\AircraftRepository;
use App\Repositories\Aircraft\AircraftRepositoryInterface;
use App\Repositories\Booking\BookingRepository;
use App\Repositories\Booking\BookingRepositoryInterface;
use App\Repositories\Passenger\PassengerRepository;
use App\Repositories\Passenger\PassengerRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(AircraftRepositoryInterface::class, AircraftRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(PassengerRepositoryInterface::class, PassengerRepository::class);
    }
}
