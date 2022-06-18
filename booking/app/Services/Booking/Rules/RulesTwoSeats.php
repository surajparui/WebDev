<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesTwoSeats
{
    /**
     * Find two seats
     * @param  array $seats
     * @param  Row $nextRow
     * @return void
     */
    private function findTwoSeats(array $seats,  ? Row $nextRow): void
    {
        if (!$seats['A']->occupied && !$seats['B']->occupied) {
            $this->occupy([$seats['A'], $seats['B']]);
            return;
        }

        if (!$seats['E']->occupied && !$seats['F']->occupied) {
            $this->occupy([$seats['E'], $seats['F']]);
            return;
        }

        if ($nextRow) {
            $this->findTwoSeats($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findTwoSeatsAwayWindow($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * find two seats away from the window
     * @param  array $seats
     * @param  Row $nextRow
     * @return void
     */
    private function findTwoSeatsAwayWindow(array $seats,  ? Row $nextRow): void
    {

        if (!$seats['B']->occupied && !$seats['C']->occupied) {
            $this->occupy([$seats['B'], $seats['C']]);
            return;
        }

        if (!$seats['D']->occupied && !$seats['E']->occupied) {
            $this->occupy([$seats['D'], $seats['E']]);
            return;
        }

        if ($nextRow) {
            $this->findTwoSeatsAwayWindow($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findTwoSeatsNearbyAcrossAisle($this->row->seats, $this->row->nextRow);
        }
    }

    /**
     * find two seats nearby across the aisle
     * @param  array $seats
     * @param  Row $nextRow
     * @return void
     */
    private function findTwoSeatsNearbyAcrossAisle(array $seats,  ? Row $nextRow): void
    {

        if (!$seats['C']->occupied && !$seats['D']->occupied) {
            $this->occupy([$seats['C'], $seats['D']]);
            return;
        }

        if ($nextRow) {
            $this->findTwoSeatsNearbyAcrossAisle($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findAnySeat($this->row->seats, $this->row->nextRow);
        }
    }
}
