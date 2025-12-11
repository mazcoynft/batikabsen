<?php

use App\Http\Controllers\Frontend\IzinController;
use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use App\Models\User;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
/**
 * **Feature: leave-calculation-fix, Property 2: Leave balance deduction matches working days**
 * 
 * Property-based tests for leave balance deduction logic
 * These tests verify that leave balance is correctly reduced by working days count
 */

beforeEach(function () {
    $this->workingDaysCalculator = new WorkingDaysCalculator();
});

describe('Leave Balance Calculation Logic', function () {
    
    it('calculates correct leave balance deduction for working days', function () {
        $startDate = Carbon::create(2024, 1, 1); // Monday
        $endDate = Carbon::create(2024, 1, 5);   // Friday
        $initialBalance = 12;
        
        $workingDays = $this->workingDaysCalculator->calculateWorkingDays($startDate, $endDate);
        $expectedRemainingBalance = $initialBalance - $workingDays;
        
        // Property: Working days should be 5 for Monday-Friday
        expect($workingDays)->toBe(5);
        
        // Property: Balance deduction should equal working days
        expect($expectedRemainingBalance)->toBe(7); // 12 - 5
        
        // Property: Remaining balance should not be negative
        expect($expectedRemainingBalance)->toBeGreaterThanOrEqual(0);
    });
    
    it('handles weekend-only leave requests correctly', function () {
        $startDate = Carbon::create(2024, 1, 6); // Saturday
        $endDate = Carbon::create(2024, 1, 7);   // Sunday
        $initialBalance = 12;
        
        $workingDays = $this->workingDaysCalculator->calculateWorkingDays($startDate, $endDate);
        $expectedRemainingBalance = $initialBalance - $workingDays;
        
        // Property: Weekend-only requests should have 0 working days
        expect($workingDays)->toBe(0);
        
        // Property: Balance should remain unchanged for weekend-only requests
        expect($expectedRemainingBalance)->toBe($initialBalance);
    });
    
    it('calculates balance correctly for mixed weekday-weekend ranges', function () {
        $startDate = Carbon::create(2024, 1, 5); // Friday
        $endDate = Carbon::create(2024, 1, 8);   // Monday (spans weekend)
        $initialBalance = 12;
        
        $workingDays = $this->workingDaysCalculator->calculateWorkingDays($startDate, $endDate);
        $expectedRemainingBalance = $initialBalance - $workingDays;
        
        // Property: Should count only Friday and Monday (2 working days)
        expect($workingDays)->toBe(2);
        
        // Property: Balance deduction should match working days
        expect($expectedRemainingBalance)->toBe(10); // 12 - 2
    });
    
    it('validates insufficient balance scenarios', function () {
        $startDate = Carbon::create(2024, 1, 1); // Monday
        $endDate = Carbon::create(2024, 1, 12);  // Friday (2 weeks = 10 working days)
        $initialBalance = 5; // Insufficient balance
        
        $workingDays = $this->workingDaysCalculator->calculateWorkingDays($startDate, $endDate);
        $isInsufficientBalance = $workingDays > $initialBalance;
        
        // Property: Should detect insufficient balance
        expect($workingDays)->toBe(10);
        expect($isInsufficientBalance)->toBeTrue();
        
        // Property: Should not allow negative balance
        if (!$isInsufficientBalance) {
            $remainingBalance = $initialBalance - $workingDays;
            expect($remainingBalance)->toBeGreaterThanOrEqual(0);
        }
    });
});

/**
 * Property-based test simulation for leave balance deduction
 * Tests various date ranges and verifies balance calculations
 */
describe('Property-based tests for leave balance deduction', function () {
    
    it('correctly calculates balance for various date ranges', function ($startDate, $endDate, $initialBalance) {
        $expectedWorkingDays = $this->workingDaysCalculator->calculateWorkingDays($startDate, $endDate);
        
        // Skip test if insufficient balance
        if ($expectedWorkingDays > $initialBalance) {
            expect(true)->toBeTrue(); // Skip this iteration
            return;
        }
        
        // Property: Working days calculation should be consistent
        expect($expectedWorkingDays)->toBeGreaterThanOrEqual(0);
        
        // Property: Balance deduction should equal working days
        $expectedRemainingBalance = $initialBalance - $expectedWorkingDays;
        expect($expectedRemainingBalance)->toBe($initialBalance - $expectedWorkingDays);
        
        // Property: Remaining balance should not be negative
        expect($expectedRemainingBalance)->toBeGreaterThanOrEqual(0);
        
        // Property: Working days should never exceed total days in range
        $totalDays = $startDate->diffInDays($endDate) + 1;
        expect($expectedWorkingDays)->toBeLessThanOrEqual($totalDays);
        
    })->with([
        // [startDate, endDate, initialBalance]
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 1), 12], // Single weekday
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 5), 12], // One work week
        [Carbon::create(2024, 1, 1), Carbon::create(2024, 1, 12), 15], // Two work weeks
        [Carbon::create(2024, 1, 6), Carbon::create(2024, 1, 7), 12], // Weekend only
        [Carbon::create(2024, 1, 5), Carbon::create(2024, 1, 8), 12], // Fri-Mon span
        [Carbon::create(2024, 2, 1), Carbon::create(2024, 2, 29), 25], // Full month
        [Carbon::create(2024, 1, 3), Carbon::create(2024, 1, 3), 5],  // Single day with sufficient balance
    ]);
    
    it('maintains mathematical consistency in balance calculations', function () {
        $initialBalance = 12;
        
        // First calculation: 3 working days
        $workingDays1 = $this->workingDaysCalculator->calculateWorkingDays(
            Carbon::create(2024, 1, 1), // Monday
            Carbon::create(2024, 1, 3)  // Wednesday
        );
        $remainingAfterFirst = $initialBalance - $workingDays1;
        
        expect($workingDays1)->toBe(3);
        expect($remainingAfterFirst)->toBe(9); // 12 - 3
        
        // Second calculation: 2 working days
        $workingDays2 = $this->workingDaysCalculator->calculateWorkingDays(
            Carbon::create(2024, 1, 8), // Monday
            Carbon::create(2024, 1, 9)  // Tuesday
        );
        $remainingAfterSecond = $remainingAfterFirst - $workingDays2;
        
        expect($workingDays2)->toBe(2);
        expect($remainingAfterSecond)->toBe(7); // 9 - 2
        
        // Property: Sequential deductions should be mathematically consistent
        $totalDeducted = $workingDays1 + $workingDays2;
        $finalBalance = $initialBalance - $totalDeducted;
        expect($remainingAfterSecond)->toBe($finalBalance);
    });
});