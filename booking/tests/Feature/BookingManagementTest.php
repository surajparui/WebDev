<?php

namespace Tests\Feature;

use App\Models\Aircraft;
use App\Models\Booking;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function a_booking_can_be_done()
    {
        $aircraft = Aircraft::factory()->create();

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $this->faker->name, 'quantity_seats' => 1],
                ],
            ]
        );

        $this->assertCount(1, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                "data" => [["passenger", "seats"]],
            ]);
    }

    /** @test */
    public function the_bookings_can_be_shown()
    {
        $aircraft = Aircraft::factory()->create();

        $names    = $this->makePassengersArray(2, 2);
        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $response = $this->getJson('/api/bookings/');

        $response
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    ["passenger" => $names[0]['name'], "seats" => "A1, B1"],
                    ["passenger" => $names[1]['name'], "seats" => "E1, F1"],
                ],
            ]);
    }

    /** @test */
    public function the_bookings_can_be_cleared()
    {
        $aircraft = Aircraft::factory()->create();

        $names    = $this->makePassengersArray(2, 2);
        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $this->assertCount(4, Booking::all());

        $response = $this->deleteJson('/api/bookings/');

        $response->assertStatus(200);

        $this->assertCount(0, Booking::all());
    }

    /** @test */
    public function an_array_of_passengers_is_required()
    {
        $aircraft = Aircraft::factory()->create();

        $response = $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, []);

        $response->assertJsonValidationErrors('passengers', 'error');
    }

    /** @test */
    public function passengers_name_and_passengers_quantity_of_seats_is_required()
    {
        $aircraft = Aircraft::factory()->create();

        $response = $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => ["test"]]);

        $response->assertJsonValidationErrors(
            ['passengers.0.name', 'passengers.0.quantity_seats'],
            'error'
        );
    }

    /** @test */
    public function passengers_quantity_of_seats_cannot_be_more_than_7()
    {
        $aircraft = Aircraft::factory()->create();

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $this->faker->name, 'quantity_seats' => 8],
                ],
            ]
        );

        $response->assertJsonValidationErrors('passengers.0.quantity_seats', 'error');
    }

    /** @test */
    public function the_booking_should_start_from_the_window()
    {
        $aircraft = Aircraft::factory()->create();

        $names    = $this->makePassengersArray(4, 1);
        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $this->assertCount(4, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $names[0]['name'], "seats" => "A1"],
                    ["passenger" => $names[1]['name'], "seats" => "F1"],
                    ["passenger" => $names[2]['name'], "seats" => "A2"],
                    ["passenger" => $names[3]['name'], "seats" => "F2"],
                ],
            ]);
    }

    /** @test */
    public function book_a_random_seat_if_there_is_no_window_seat_available()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 1],
                ],
            ]
        );

        $this->assertCount(53, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B1"],
                ],
            ]);
    }

    /** @test */
    public function an_error_is_returned_if_there_is_no_seat_available()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 3);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 1],
                ],
            ]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'No seats available']);
    }

    /** @test */
    public function an_error_is_returned_if_there_are_no_enough_seats_to_book()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(53, 3);

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'There are no enough seats for this booking']);
    }

    /** @test */
    public function book_2_passengers_in_the_same_row()
    {
        $aircraft = Aircraft::factory()->create();
        $names    = [];
        //filling the first window
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 1]);
        //test 2 seats in a row
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 2]);

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $this->assertCount(3, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $names[0]['name'], "seats" => "A1"],
                    ["passenger" => $names[1]['name'], "seats" => "E1, F1"],
                ],
            ]);
    }

    /** @test */
    public function book_2_passengers_in_the_same_row_and_away_of_the_windows_without_crossing_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(53, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 2],
                ],
            ]
        );

        $this->assertCount(55, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "D1, E1"],
                ],
            ]);
    }

    /** @test */
    public function book_2_passengers_nearby_across_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 2);
        //filling one seat alone
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 1]);

        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 2],
                ],
            ]
        );

        $this->assertCount(107, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "C2, D2"],
                ],
            ]);
    }

    /** @test */
    public function book_3_passengers_in_the_same_row()
    {
        $aircraft = Aircraft::factory()->create();
        $names    = [];
        //filling the first window
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 1]);
        //test 3 seats in a row
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 3]);

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            ['passengers' => $names]
        );

        $this->assertCount(4, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $names[0]['name'], "seats" => "A1"],
                    ["passenger" => $names[1]['name'], "seats" => "D1, E1, F1"],
                ],
            ]);
    }

    /** @test */
    public function book_3_passengers_balancing_them_across_rows()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 3],
                ],
            ]
        );

        $this->assertCount(55, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B1, C1, B2"],
                ],
            ]);
    }

    /** @test */
    public function book_4_passengers_balancing_them_across_rows()
    {
        $aircraft = Aircraft::factory()->create();
        $name     = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 4],
                ],
            ]
        );

        $this->assertCount(4, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "A1, B1, A2, B2"],
                ],
            ]);
    }

    /** @test */
    public function book_4_passengers_balancing_them_across_rows_and_away_of_the_windows()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 4],
                ],
            ]
        );

        $this->assertCount(56, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B1, C1, B2, C2"],
                ],
            ]);
    }

    /** @test */
    public function book_4_passengers_nearby_across_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 2);
        //more one to make the test
        array_push($names, ['name' => $this->faker->name, 'quantity_seats' => 1]);

        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 4],
                ],
            ]
        );

        $this->assertCount(109, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "C2, D2, C3, D3"],
                ],
            ]);
    }

    /** @test */
    public function book_5_passengers_balancing_them_across_rows()
    {
        $aircraft = Aircraft::factory()->create();
        $name     = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 5],
                ],
            ]
        );

        $this->assertCount(5, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "A1, B1, C1, A2, B2"],
                ],
            ]);
    }

    /** @test */
    public function book_5_passengers_nearby_across_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 5],
                ],
            ]
        );

        $this->assertCount(57, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B1, C1, D1, B2, C2"],
                ],
            ]);
    }

    /** @test */
    public function book_6_passengers_balancing_them_across_rows()
    {
        $aircraft = Aircraft::factory()->create();
        $name     = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 6],
                ],
            ]
        );

        $this->assertCount(6, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "A1, B1, C1, A2, B2, C2"],
                ],
            ]);
    }

    /** @test */
    public function book_6_passengers_nearby_across_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(52, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 6],
                ],
            ]
        );

        $this->assertCount(58, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B1, C1, D1, B2, C2, D2"],
                ],
            ]);
    }

    /** @test */
    public function book_7_passengers_balancing_them_across_rows_and_nearby_across_the_aisle()
    {
        $aircraft = Aircraft::factory()->create();
        $name     = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 7],
                ],
            ]
        );

        $this->assertCount(7, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "A1, B1, C1, D1, A2, B2, C2"],
                ],
            ]);
    }

    /** @test */
    public function book_7_passengers_away_of_the_windows()
    {
        $aircraft = Aircraft::factory()->create();

        $names = $this->makePassengersArray(54, 1);
        $this->postJson('/api/bookings/aircrafts/' . $aircraft->id, ['passengers' => $names]);

        $name = $this->faker->name;

        $response = $this->postJson(
            '/api/bookings/aircrafts/' . $aircraft->id,
            [
                'passengers' => [
                    ['name' => $name, 'quantity_seats' => 7],
                ],
            ]
        );

        $this->assertCount(61, Booking::all());

        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    ["passenger" => $name, "seats" => "B2, C2, D2, E2, B3, C3, D3"],
                ],
            ]);
    }

    /**
     * make an array with passengers' name and quantity to be sent
     * @param  int    $numberOfPassengers
     * @param  int    $quantityOfSeats
     * @return array
     */
    private function makePassengersArray(int $numberOfPassengers, int $quantityOfSeats): array
    {
        $names = [];
        for ($i = 0; $i < $numberOfPassengers; $i++) {
            array_push($names, [
                'name'           => $this->faker->name,
                'quantity_seats' => $quantityOfSeats,
            ]);
        }
        return $names;
    }
}
