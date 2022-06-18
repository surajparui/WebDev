<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['aircraft_id', 'passenger_id', 'row_number', 'row_seat'];

    /**
     * @return belongsTo
     */
    public function passenger()
    {
        return $this->belongsTo('App\Models\Passenger');
    }
}
