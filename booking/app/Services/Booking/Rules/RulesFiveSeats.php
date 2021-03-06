<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesFiveSeats
{
    /**
     * Find 
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findFiveSeats(array $seats,  ? Row $nextRow) : void
    {
        if ($nextRow &&
            !$seats['A']->occupied &&
            !$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['A']->occupied &&
            !$nextRow->seats['B']->occupied) {
            $this->occupy([
                $seats['A'],
                $seats['B'],
                $seats['C'],
                $nextRow->seats['A'],
                $nextRow->seats['B'],
            ]);
            return;
        }

        if ($nextRow &&
            !$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['A']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied) {
            $this->occupy([
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
            !$nextRow->seats['E']->occupied &&
            !$nextRow->seats['F']->occupied) {
            $this->occupy([
                $seats['D'],
                $seats['E'],
                $seats['F'],
                $nextRow->seats['E'],
                $nextRow->seats['F'],
            ]);
            return;
        }

        if ($nextRow &&
            !$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied &&
            !$nextRow->seats['F']->occupied) {
            $this->occupy([
                $seats['D'],
                $seats['E'],
                $nextRow->seats['D'],
                $nextRow->seats['E'],
                $nextRow->seats['F'],
            ]);
            return;
        }

        if ($nextRow) {
            $this->findFiveSeats($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findFiveSeatsNearbyAcrossAisle($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * Find seats
     * @param  array $seats
     * @param  Row   $nextRow
     * @return void
     */
    private function findFiveSeatsNearbyAcrossAisle(array $seats,  ? Row $nextRow) : void
    {
        if (!$nextRow) {
            $this->findAnySeat($this->row->seats, $this->row->nextRow);
            return;
        }

        if (!$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$seats['D']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied) {
            $this->occupy([
                $seats['B'],
                $seats['C'],
                $seats['D'],
                $nextRow->seats['B'],
                $nextRow->seats['C'],
            ]);
            return;
        }

        if (!$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied &&
            !$nextRow->seats['D']->occupied) {
            $this->occupy([
                $seats['B'],
                $seats['C'],
                $nextRow->seats['B'],
                $nextRow->seats['C'],
                $nextRow->seats['D'],
            ]);
            return;
        }

        if (!$seats['C']->occupied &&
            !$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([
                $seats['C'],
                $seats['D'],
                $seats['E'],
                $nextRow->seats['D'],
                $nextRow->seats['E'],
            ]);
            return;
        }

        if (!$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['C']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([
                $seats['D'],
                $seats['E'],
                $nextRow->seats['C'],
                $nextRow->seats['D'],
                $nextRow->seats['E'],
            ]);
            return;
        }

        $this->findFiveSeatsNearbyAcrossAisle($nextRow->seats, $nextRow->nextRow);
    }
}
