<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesOneSeat
{
    /**
     * Find any seat 
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return bool|callable
     */
    private function findAnySeat(array $seats,  ? Row $nextRow)
    {
        foreach ($seats as $seat) {
            if (!$seat->occupied) {
                $this->occupy([$seat]);
                return true;
            }
        }

        if ($nextRow) {
            return $this->findAnySeat($nextRow->seats, $nextRow->nextRow);
        } else {
            return false;
        }
    }

    /**
     * Find a window seat
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return bool|callable
     */
    private function findAseatOnWindow(array $seats,  ? Row $nextRow)
    {
        foreach ($seats as $seat) {
            if (!$seat->occupied && $seat->isWindow) {
                $this->occupy([$seat]);
                return true;
            }
        }

        if ($nextRow) {
            return $this->findAseatOnWindow($nextRow->seats, $nextRow->nextRow);
        } else {
            return $this->findAnySeat($this->row->seats, $this->row->nextRow);
        }
    }
}