<?php
namespace App\Repositories\Aircraft;

use App\Models\Aircraft;
use App\Repositories\Aircraft\AircraftRepositoryInterface;
use App\Repositories\BaseRepository;

class AircraftRepository extends BaseRepository implements AircraftRepositoryInterface
{

    public function __construct(Aircraft $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  int $id
     * @return array
     */
    public function getWithBookings(int $id): array
    {
        return $this->model->with('bookings')->find($id)->toArray();
    }

}
