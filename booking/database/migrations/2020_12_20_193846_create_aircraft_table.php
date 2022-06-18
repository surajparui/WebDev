<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('aircraft_type_id');
            $table->unsignedTinyInteger('sits_count');
            $table->unsignedTinyInteger('rows');
            $table->string('rows_arrangement', 100)->default('A B C _ D E F');
            $table->timestamps();

            $table->foreign('aircraft_type_id')->references('id')->on('aircraft_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aircraft');
    }
}
