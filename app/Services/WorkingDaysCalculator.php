<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class WorkingDaysCalculator
{
    /**
     * Calculate the number of working days between two dates (inclusive)
     * Working days are Monday through Friday, excluding weekends
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     * @throws InvalidArgumentException
     */
    public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        // Validate input dates
        if ($startDate->isAfter($endDate)) {
            return 0;
        }

        // If same day, check if it's a working day
        if ($startDate->isSameDay($endDate)) {
            return $this->isWeekend($startDate) ? 0 : 1;
        }

        $workingDays = 0;
        $currentDate = $startDate->copy();

        // Iterate through each day in the range
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            if (!$this->isWeekend($currentDate)) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Check if a given date falls on a weekend (Saturday or Sunday)
     *
     * @param Carbon $date
     * @return bool
     */
    public function isWeekend(Carbon $date): bool
    {
        // Carbon::SATURDAY = 6, Carbon::SUNDAY = 0
        return $date->dayOfWeek === Carbon::SATURDAY || $date->dayOfWeek === Carbon::SUNDAY;
    }

    /**
     * Get a collection of all working days within a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function getWorkingDaysInRange(Carbon $startDate, Carbon $endDate): Collection
    {
        // Validate input dates
        if ($startDate->isAfter($endDate)) {
            return collect();
        }

        $workingDays = collect();
        $currentDate = $startDate->copy();

        // Iterate through each day in the range
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            if (!$this->isWeekend($currentDate)) {
                $workingDays->push($currentDate->copy());
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Calculate working days using an optimized algorithm for large date ranges
     * This method is more efficient for ranges spanning many weeks
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public function calculateWorkingDaysOptimized(Carbon $startDate, Carbon $endDate): int
    {
        // Validate input dates
        if ($startDate->isAfter($endDate)) {
            return 0;
        }

        // If same day, check if it's a working day
        if ($startDate->isSameDay($endDate)) {
            return $this->isWeekend($startDate) ? 0 : 1;
        }

        $start = $startDate->copy();
        $end = $endDate->copy();
        
        // Calculate full weeks
        $totalDays = $start->diffInDays($end) + 1;
        $fullWeeks = intval($totalDays / 7);
        $workingDays = $fullWeeks * 5; // 5 working days per week

        // Handle remaining days
        $remainingDays = $totalDays % 7;
        if ($remainingDays > 0) {
            // Calculate working days in the partial week
            $partialStart = $start->copy()->addDays($fullWeeks * 7);
            $partialEnd = $end->copy();
            
            $currentDate = $partialStart;
            while ($currentDate->lessThanOrEqualTo($partialEnd)) {
                if (!$this->isWeekend($currentDate)) {
                    $workingDays++;
                }
                $currentDate->addDay();
            }
        }

        return $workingDays;
    }

    /**
     * Check if a date range contains only weekend days
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return bool
     */
    public function isWeekendOnlyRange(Carbon $startDate, Carbon $endDate): bool
    {
        return $this->calculateWorkingDays($startDate, $endDate) === 0;
    }

    /**
     * Get the next working day after a given date
     *
     * @param Carbon $date
     * @return Carbon
     */
    public function getNextWorkingDay(Carbon $date): Carbon
    {
        $nextDay = $date->copy()->addDay();
        
        while ($this->isWeekend($nextDay)) {
            $nextDay->addDay();
        }
        
        return $nextDay;
    }

    /**
     * Get the previous working day before a given date
     *
     * @param Carbon $date
     * @return Carbon
     */
    public function getPreviousWorkingDay(Carbon $date): Carbon
    {
        $previousDay = $date->copy()->subDay();
        
        while ($this->isWeekend($previousDay)) {
            $previousDay->subDay();
        }
        
        return $previousDay;
    }
}