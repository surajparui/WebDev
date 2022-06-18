<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesFourSeats
{
    /**
     * Find
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findFourSeats(array $seats,  ? Row $nextRow): void
    {
        if ($nextRow &&
            !$seats['A']->occupied &&
            !$seats['B']->occupied &&
            !$nextRow->seats['A']->occupied &&
            !$nextRow->seats['B']->occupied) {
            $this->occupy([
                $seats['A'], $seats['B'], $nextRow->seats['A'], $nextRow->seats['B'],
            ]);
            return;
        }

        if ($nextRow &&
            !$seats['E']->occupied &&
            !$seats['F']->occupied &&
            !$nextRow->seats['E']->occupied &&
            !$nextRow->seats['F']->occupied) {
            $this->occupy([
                $seats['E'], $seats['F'], $nextRow->seats['E'], $nextRow->seats['F'],
            ]);
            return;
        }

        if ($nextRow) {
            $this->findFourSeats($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findFourSeatsAwayWindow($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * Find four seats away from the window
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findFourSeatsAwayWindow(array $seats,  ? Row $nextRow): void
    {
        if ($nextRow &&
            !$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied) {
            $this->occupy([
                $seats['B'], $seats['C'], $nextRow->seats['B'], $nextRow->seats['C'],
            ]);
            return;
        }

        if ($nextRow &&
            !$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([
                $seats['D'], $seats['E'], $nextRow->seats['D'], $nextRow->seats['E'],
            ]);
            return;
        }

        if ($nextRow) {
            $this->findFourSeatsAwayWindow($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findFourSeatsNearbyAcrossAisle($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * Find four seats Nearby across the aisle
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findFourSeatsNearbyAcrossAisle(array $seats,  ? Row $nextRow): void
    {
        if (!$nextRow) {
            $this->findAnySeat($this->row->seats, $this->row->nextRow);
            return;
        }

        if (!$seats['C']->occupied &&
            !$seats['D']->occupied &&
            !$nextRow->seats['C']->occupied &&
            !$nextRow->seats['D']->occupied) {
            $this->occupy([
                $seats['C'], $seats['D'], $nextRow->seats['C'], $nextRow->seats['D'],
            ]);
            return;
        }

        $this->findFourSeatsNearbyAcrossAisle($nextRow->seats, $nextRow->nextRow);
    }
}
