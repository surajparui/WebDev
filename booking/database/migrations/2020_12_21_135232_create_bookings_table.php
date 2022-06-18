<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('aircraft_id');
            $table->unsignedInteger('passenger_id');
            $table->unsignedTinyInteger('row_number');
            $table->string('row_seat', 1)->comment('Ex. A, B or C');
            $table->unsignedTinyInteger('canceled')->default(0)->comment('1 is canceled');
            $table->timestamps();

            $table->foreign('passenger_id')->references('id')->on('passengers');
            $table->foreign('aircraft_id')->references('id')->on('aircraft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
