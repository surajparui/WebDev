<?php

namespace Tests\Unit;

use App\Models\Aircraft;
use App\Models\Booking;
use App\Models\Passenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function a_booking_can_be_done()
    {
        $aircraft  = Aircraft::factory()->create();
        $passenger = Passenger::factory()->create();

        Booking::factory()->create([
            'aircraft_id'  => $aircraft->id,
            'passenger_id' => $passenger->id,
        ]);

        $this->assertCount(1, Booking::all());
        $booking = Booking::first();
        $this->assertEquals($aircraft->id, $booking->aircraft_id);
        $this->assertEquals($passenger->id, $booking->passenger_id);
    }
}
