<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesSixSeats
{
    /**
     * Find six seats
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findSixSeats(array $seats,  ? Row $nextRow) : void
    {
        if ($nextRow &&
            !$seats['A']->occupied &&
            !$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['A']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied) {
            $this->occupy([
                $seats['A'],
                $seats['B'],
                $seats['C'],
                $nextRow->seats['A'],
                $nextRow->seats['B'],
                $nextRow->seats['C'],
            ]);
            return;
        }

        if ($nextRow &&
            !$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$seats['F']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied &&
            !$nextRow->seats['F']->occupied) {
            $this->occupy([
                $seats['D'],
                $seats['E'],
                $seats['F'],
                $nextRow->seats['D'],
                $nextRow->seats['E'],
                $nextRow->seats['F'],
            ]);
            return;
        }

        if ($nextRow) {
            $this->findSixSeats($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findSixSeatsNearbyAcrossAisle($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * Find six seats nearby 
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findSixSeatsNearbyAcrossAisle(array $seats,  ? Row $nextRow) : void
    {
        if (!$nextRow) {
            $this->findAnySeat($this->row->seats, $this->row->nextRow);
            return;
        }

        if (!$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$seats['D']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied &&
            !$nextRow->seats['D']->occupied) {
            $this->occupy([
                $seats['B'],
                $seats['C'],
                $seats['D'],
                $nextRow->seats['B'],
                $nextRow->seats['C'],
                $nextRow->seats['D'],
            ]);
            return;
        }

        if (!$seats['C']->occupied &&
            !$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['C']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([
                $seats['C'],
                $seats['D'],
                $seats['E'],
                $nextRow->seats['C'],
                $nextRow->seats['D'],
                $nextRow->seats['E'],
            ]);
            return;
        }

        $this->findSixSeatsNearbyAcrossAisle($nextRow->seats, $nextRow->nextRow);
    }
}
