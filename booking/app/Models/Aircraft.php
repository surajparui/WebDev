<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    use HasFactory;

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking')->where('canceled', 0);
    }
}
