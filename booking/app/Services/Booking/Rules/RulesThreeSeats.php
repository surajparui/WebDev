<?php

namespace App\Services\Booking\Rules;

use App\Services\Booking\Objects\Row;

trait RulesThreeSeats
{
    /**
     * Find three seats
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findThreeSeats(array $seats,  ? Row $nextRow): void
    {
        if (!$seats['A']->occupied &&
            !$seats['B']->occupied &&
            !$seats['C']->occupied) {
            $this->occupy([$seats['A'], $seats['B'], $seats['C']]);
            return;
        }

        if (!$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$seats['F']->occupied) {
            $this->occupy([$seats['D'], $seats['E'], $seats['F']]);
            return;
        }

        if ($nextRow) {
            $this->findThreeSeats($nextRow->seats, $nextRow->nextRow);
        } else {
            $this->findThreeSeatsBalancingAcrossRows(
                $this->row->seats, $this->row->nextRow
            );
        }
    }

    /**
     * Find three seats
     * @param  array  $seats
     * @param  Row    $nextRow
     * @return void
     */
    private function findThreeSeatsBalancingAcrossRows(array $seats,  ? Row $nextRow): void
    {
        if (!$nextRow) {
            $this->findAnySeat($this->row->seats, $this->row->nextRow);
            return;
        }

        if (!$seats['B']->occupied &&
            !$seats['C']->occupied &&
            !$nextRow->seats['B']->occupied) {
            $this->occupy([$seats['B'], $seats['C'], $nextRow->seats['B']]);
            return;
        }

        if (!$seats['C']->occupied &&
            !$nextRow->seats['B']->occupied &&
            !$nextRow->seats['C']->occupied) {
            $this->occupy([
                $seats['C'], $nextRow->seats['B'], $nextRow->seats['C'],
            ]);
            return;
        }

        if (!$seats['D']->occupied &&
            !$seats['E']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([$seats['D'], $seats['E'], $nextRow->seats['E']]);
            return;
        }

        if (!$seats['D']->occupied &&
            !$nextRow->seats['D']->occupied &&
            !$nextRow->seats['E']->occupied) {
            $this->occupy([
                $seats['D'], $nextRow->seats['D'], $nextRow->seats['E'],
            ]);
            return;
        }

        $this->findThreeSeatsBalancingAcrossRows(
            $nextRow->seats, $nextRow->nextRow
        );
    }
}
