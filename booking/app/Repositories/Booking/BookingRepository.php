<?php
namespace App\Repositories\Booking;

use App\Models\Booking;
use App\Repositories\BaseRepository;
use App\Repositories\Booking\BookingRepositoryInterface;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{

    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    /**
     * @return void
     */
    public function deleteAll(): void
    {
        $this->model->get()->each->delete();
    }
}
