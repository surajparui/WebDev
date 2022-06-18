<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreRequest;
use App\Repositories\Aircraft\AircraftRepositoryInterface as AircraftRepository;
use App\Repositories\Booking\BookingRepositoryInterface as BookingRepository;
use App\Repositories\Passenger\PassengerRepositoryInterface as PassengerRepository;
use App\Services\Booking\BookingService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use ApiResponser;
    protected $bookingService;

    protected $bookingRepository;

    public function __construct(BookingRepository $bookingRepository, BookingService $bookingService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->bookingService    = $bookingService;
    }

    /**
     * @param  PassengerRepository $passengerRepository
     * @return JsonResponse
     */
    public function index(PassengerRepository $passengerRepository)
    {
        $passengers = $passengerRepository->getWithBookings();
        $bookings   = $this->bookingService->makeResponse($passengers);

        return $this->successResponse($bookings);
    }

    /**
     * Store a newly created resource in storage.
     * @param  StoreRequest        $request
     * @param  int                 $aircraftId
     * @param  AircraftRepository  $aircraftRepository
     * @param  PassengerRepository $passengerRepository
     * @return JsonResponse
     */
    public function store(StoreRequest $request, int $aircraftId, AircraftRepository $aircraftRepository, PassengerRepository $passengerRepository)
    {
        $aircraft = $aircraftRepository->getWithBookings($aircraftId);

        $this->bookingService->validateSeatsAvailable($aircraft, $request->passengers);

        $bookingsToDo = $this->bookingService->getSeatsAvailable($aircraft, $request->passengers);

        $bookingsDone = [];
        foreach ($bookingsToDo as $booking) {
            array_push($bookingsDone, $this->saveInDataBase($booking, $passengerRepository));
        }

        $bookings = $this->bookingService->makeResponse($bookingsDone);

        return $this->successResponse($bookings, Response::HTTP_CREATED);
    }

    /**
     * Saving the bookings in the database
     * @param  array               $booking
     * @param  PassengerRepository $passengerRepository
     * @return array
     */
    private function saveInDataBase(array $booking, PassengerRepository $passengerRepository): array
    {
        DB::beginTransaction();

        $passenger = $passengerRepository->create(['name' => $booking['passenger']]);

        $seats = [];
        foreach ($booking['seats'] as $seat) {
            $seats[] = $this->bookingRepository->create(
                array_merge($seat, ['passenger_id' => $passenger['id']])
            );
        }

        DB::commit();

        return ['name' => $passenger['name'], 'bookings' => $seats];
    }

    /**
     * Remove all the resource from storage.
     * @return JsonResponse
     */
    public function destroyAll(PassengerRepository $passengerRepository)
    {
        $this->bookingRepository->deleteAll();
        $passengerRepository->deleteAll();

        return $this->successResponse([]);
    }
}
