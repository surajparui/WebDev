<?php
namespace App\Repositories\Passenger;

use App\Models\Passenger;
use App\Repositories\Passenger\PassengerRepositoryInterface;
use App\Repositories\BaseRepository;

class PassengerRepository extends BaseRepository implements PassengerRepositoryInterface
{

    public function __construct(Passenger $model)
    {
        parent::__construct($model);
    }

    /**
     * @return array
     */
    public function getWithBookings(): array
    {
        return $this->model->with('bookings')->get()->toArray();
    }

    /**
     * @return void
     */
    public function deleteAll(): void
    {
        $this->model->get()->each->delete();
    }
}
