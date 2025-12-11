<?php

use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;

/**
 * **Feature: leave-calculation-fix, Property 1: Working days calculation excludes weekends**
 * 
 * Property-based tests for WorkingDaysCalculator service
 * These tests verify that working days calculation correctly excludes weekends
 * and handles various date range scenarios.
 */

beforeEach(function () {
    $this->calculator = new WorkingDaysCalculator();
});

describe('WorkingDaysCalculator', function () {
    
    describe('calculateWorkingDays', function () {
        
        it('calculates working days correctly for a single weekday', function () {
            // Monday
            $monday = Carbon::create(2024, 1, 1); // January 1, 2024 is a Monday
            $result = $this->calculator->calculateWorkingDays($monday, $monday);
            expect($result)->toBe(1);
        });

        it('returns zero for a single weekend day', function () {
            // Saturday
            $saturday = Carbon::create(2024, 1, 6); // January 6, 2024 is a Saturday
            $result = $this->calculator->calculateWorkingDays($saturday, $saturday);
            expect($result)->toBe(0);
            
            // Sunday
            $sunday = Carbon::create(2024, 1, 7); // January 7, 2024 is a Sunday
            $result = $this->calculator->calculateWorkingDays($sunday, $sunday);
            expect($result)->toBe(0);
        });

        it('calculates exactly 5 working days for Monday to Friday', function () {
            $monday = Carbon::create(2024, 1, 1); // Monday
            $friday = Carbon::create(2024, 1, 5); // Friday
            $result = $this->calculator->calculateWorkingDays($monday, $friday);
            expect($result)->toBe(5);
        });

        it('excludes weekends from multi-week ranges', function () {
            // 2 weeks: Monday Jan 1 to Sunday Jan 14, 2024
            $start = Carbon::create(2024, 1, 1); // Monday
            $end = Carbon::create(2024, 1, 14); // Sunday
            $result = $this->calculator->calculateWorkingDays($start, $end);
            expect($result)->toBe(10); // 2 weeks × 5 working days
        });

        it('returns zero when start date is after end date', function () {
            $start = Carbon::create(2024, 1, 5);
            $end = Carbon::create(2024, 1, 1);
            $result = $this->calculator->calculateWorkingDays($start, $end);
            expect($result)->toBe(0);
        });

        it('handles ranges that span only weekends', function () {
            // Saturday to Sunday
            $saturday = Carbon::create(2024, 1, 6);
            $sunday = Carbon::create(2024, 1, 7);
            $result = $this->calculator->calculateWorkingDays($saturday, $sunday);
            expect($result)->toBe(0);
        });

        it('handles ranges starting on weekend', function () {
            // Saturday to next Tuesday
            $saturday = Carbon::create(2024, 1, 6); // Saturday
            $tuesday = Carbon::create(2024, 1, 9); // Tuesday
            $result = $this->calculator->calculateWorkingDays($saturday, $tuesday);
            expect($result)->toBe(2); // Monday and Tuesday only
        });

        it('handles ranges ending on weekend', function () {
            // Thursday to Saturday
            $thursday = Carbon::create(2024, 1, 4); // Thursday
            $saturday = Carbon::create(2024, 1, 6); // Saturday
            $result = $this->calculator->calculateWorkingDays($thursday, $saturday);
            expect($result)->toBe(2); // Thursday and Friday only
        });
    });

    describe('isWeekend', function () {
        
        it('correctly identifies Saturday as weekend', function () {
            $saturday = Carbon::create(2024, 1, 6); // Saturday
            expect($this->calculator->isWeekend($saturday))->toBeTrue();
        });

        it('correctly identifies Sunday as weekend', function () {
            $sunday = Carbon::create(2024, 1, 7); // Sunday
            expect($this->calculator->isWeekend($sunday))->toBeTrue();
        });

        it('correctly identifies weekdays as non-weekend', function () {
            $weekdays = [
                Carbon::create(2024, 1, 1), // Monday
                Carbon::create(2024, 1, 2), // Tuesday
                Carbon::create(2024, 1, 3), // Wednesday
                Carbon::create(2024, 1, 4), // Thursday
                Carbon::create(2024, 1, 5), // Friday
            ];

            foreach ($weekdays as $weekday) {
                expect($this->calculator->isWeekend($weekday))->toBeFalse();
            }
        });
    });

    describe('getWorkingDaysInRange', function () {
        
        it('returns correct working days for a week', function () {
            $monday = Carbon::create(2024, 1, 1);
            $sunday = Carbon::create(2024, 1, 7);
            $workingDays = $this->calculator->getWorkingDaysInRange($monday, $sunday);
            
            expect($workingDays)->toHaveCount(5);
            
            // Verify each day is a weekday
            foreach ($workingDays as $day) {
                expect($this->calculator->isWeekend($day))->toBeFalse();
            }
        });

        it('returns empty collection for weekend-only range', function () {
            $saturday = Carbon::create(2024, 1, 6);
            $sunday = Carbon::create(2024, 1, 7);
            $workingDays = $this->calculator->getWorkingDaysInRange($saturday, $sunday);
            
            expect($workingDays)->toHaveCount(0);
        });

        it('returns empty collection when start date is after end date', function () {
            $start = Carbon::create(2024, 1, 5);
            $end = Carbon::create(2024, 1, 1);
            $workingDays = $this->calculator->getWorkingDaysInRange($start, $end);
            
            expect($workingDays)->toHaveCount(0);
        });
    });

    describe('isWeekendOnlyRange', function () {
        
        it('returns true for weekend-only ranges', function () {
            $saturday = Carbon::create(2024, 1, 6);
            $sunday = Carbon::create(2024, 1, 7);
            
            expect($this->calculator->isWeekendOnlyRange($saturday, $sunday))->toBeTrue();
        });

        it('returns false for ranges containing weekdays', function () {
            $friday = Carbon::create(2024, 1, 5);
            $sunday = Carbon::create(2024, 1, 7);
            
            expect($this->calculator->isWeekendOnlyRange($friday, $sunday))->toBeFalse();
        });
    });

    describe('helper methods', function () {
        
        it('finds next working day correctly', function () {
            $friday = Carbon::create(2024, 1, 5); // Friday
            $nextWorkingDay = $this->calculator->getNextWorkingDay($friday);
            
            expect($nextWorkingDay->dayOfWeek)->toBe(Carbon::MONDAY);
            expect($nextWorkingDay->format('Y-m-d'))->toBe('2024-01-08');
        });

        it('finds previous working day correctly', function () {
            $monday = Carbon::create(2024, 1, 8); // Monday
            $previousWorkingDay = $this->calculator->getPreviousWorkingDay($monday);
            
            expect($previousWorkingDay->dayOfWeek)->toBe(Carbon::FRIDAY);
            expect($previousWorkingDay->format('Y-m-d'))->toBe('2024-01-05');
        });
    });
});

/**
 * Property-based test simulation using data providers
 * These tests run multiple iterations with different date ranges
 * to verify the working days calculation properties hold true
 */
describe('Property-based tests for working days calculation', function () {
    
    it('excludes weekends for random date ranges', function ($startDate, $endDate, $expectedMinDays, $expectedMaxDays) {
        $calculator = new WorkingDaysCalculator();
        $result = $calculator->calculateWorkingDays($startDate, $endDate);
        
        // Property: Result should be between expected min and max
        expect($result)->toBeGreaterThanOrEqual($expectedMinDays);
        expect($result)->toBeLessThanOrEqual($expectedMaxDays);
        
        // Property: Working days should never exceed total days
        $totalDays = $startDate->diffInDays($endDate) + 1;
        expect($result)->toBeLessThanOrEqual($totalDays);
        
        // Property: Working days should be consistent with manual calculation
        $manualCount = 0;
        $current = $startDate->copy();
        while ($current->lessThanOrEqualTo($endDate)) {
            if (!$calculator->isWeekend($current)) {
                $manualCount++;
            }
            $current->addDay();
        }
        expect($result)->toBe($manualCount);
        
    })->with([
        // [startDate, endDate, expectedMinDays, expectedMaxDays]
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 7), 5, 5], // Full week
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 14), 10, 10], // Two weeks
        [Carbon::create(2024, 1, 6), Carbon::create(2024, 1, 7), 0, 0], // Weekend only
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 31), 22, 23], // Full month
        [Carbon::create(2024, 1, 3), Carbon::create(2024, 1, 3), 1, 1], // Single weekday
        [Carbon::create(2024, 1, 6), Carbon::create(2024, 1, 6), 0, 0], // Single weekend day
        [Carbon::create(2024, 2, 29), Carbon::create(2024, 3, 1), 1, 2], // Leap year boundary
        [Carbon::create(2024, 12, 30), Carbon::create(2025, 1, 3), 4, 5], // Year boundary (Mon-Fri)
    ]);

    it('maintains consistency across different calculation methods', function ($startDate, $endDate) {
        $calculator = new WorkingDaysCalculator();
        
        $standardResult = $calculator->calculateWorkingDays($startDate, $endDate);
        $optimizedResult = $calculator->calculateWorkingDaysOptimized($startDate, $endDate);
        
        // Property: Both methods should return the same result
        expect($standardResult)->toBe($optimizedResult);
        
    })->with([
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 7)],
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 14)],
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 31)],
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 12, 31)],
        [Carbon::create(2024, 6, 15), Carbon::create(2024, 8, 15)],
    ]);
});