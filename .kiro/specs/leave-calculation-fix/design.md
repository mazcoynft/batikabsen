# Design Document

## Overview

This design document outlines the implementation approach for fixing the leave calculation logic to exclude weekends (Saturday and Sunday) from leave day calculations. The current system uses a simple date difference calculation that includes all days. We will implement a working days calculation that only counts Monday through Friday.

## Architecture

The solution will modify the existing leave calculation logic in the `IzinController` class and potentially create a reusable service for working days calculations. The architecture maintains the current MVC pattern while adding a utility service for date calculations.

### Components Affected:
- `App\Http\Controllers\Frontend\IzinController` - Main controller handling leave requests
- `App\Models\PengajuanIzin` - Leave request model (potential accessor methods)
- New utility service for working days calculation

## Components and Interfaces

### WorkingDaysCalculator Service

A new service class that provides working days calculation functionality:

```php
class WorkingDaysCalculator
{
    public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    public function isWeekend(Carbon $date): bool
    public function getWorkingDaysInRange(Carbon $startDate, Carbon $endDate): Collection
}
```

### Modified IzinController

The `store()` method will be updated to use the new working days calculation:

```php
// Before: $jumlah_hari_izin = $tanggal_awal->diffInDays($tanggal_akhir) + 1;
// After: $jumlah_hari_izin = $workingDaysCalculator->calculateWorkingDays($tanggal_awal, $tanggal_akhir);
```

## Data Models

No changes to database schema are required. The existing `pengajuan_izin` table structure remains the same:
- `jumlah_hari` field will now store working days count instead of total days
- All other fields remain unchanged

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After reviewing the prework analysis, several properties can be consolidated to eliminate redundancy:

- Properties 1.1, 1.2, and 3.1 all test the core working days calculation and can be combined into one comprehensive property
- Properties 1.4, 2.2 test display consistency and can be combined
- Properties 2.1, 2.3 test consistency across leave types and reports and can be combined
- Edge cases 1.5, 3.2, 3.3 can be handled by the property testing framework's edge case generation

### Correctness Properties

Property 1: Working days calculation excludes weekends
*For any* date range, the working days calculation should only count Monday through Friday and exclude all Saturday and Sunday dates within the range
**Validates: Requirements 1.1, 1.2, 3.1**

Property 2: Leave balance deduction matches working days
*For any* leave request, the amount deducted from leave balance should equal the calculated working days count
**Validates: Requirements 1.3**

Property 3: Consistent calculation across leave types
*For any* leave type (sick, annual, personal), the working days calculation should produce the same result for identical date ranges
**Validates: Requirements 2.1, 2.3**

Property 4: UI display consistency
*For any* leave request, the displayed duration in both employee and admin interfaces should match the calculated working days count
**Validates: Requirements 1.4, 2.2**

Property 5: One work week equals five days
*For any* date range that spans exactly Monday to Friday of the same week, the calculation should return exactly 5 working days
**Validates: Requirements 3.4**

## Error Handling

The working days calculator will handle edge cases gracefully:

- **Invalid date ranges**: When start date is after end date, return 0 days
- **Same day requests**: Single day requests return 1 if weekday, 0 if weekend
- **Null dates**: Throw appropriate validation exceptions
- **Future date validation**: Maintain existing validation in controller

## Testing Strategy

### Unit Testing Approach

Unit tests will cover:
- WorkingDaysCalculator service methods with specific date examples
- Edge cases like single days, weekend-only ranges, and boundary conditions
- Integration with existing IzinController logic

### Property-Based Testing Approach

Property-based tests will use **PHPUnit with Eris** library for property testing. Each property test will run a minimum of 100 iterations with randomly generated date ranges.

**Property test requirements:**
- Each property-based test must be tagged with: `**Feature: leave-calculation-fix, Property {number}: {property_text}**`
- Tests will generate random date ranges and verify the correctness properties hold
- Edge case generators will include weekend-only ranges, single days, and multi-week spans
- All property tests must validate against the specific requirements they implement

**Test generators will create:**
- Random date ranges spanning various periods (days, weeks, months)
- Date ranges that include different weekend patterns
- Boundary cases where ranges start/end on weekends
- Single-day requests on both weekdays and weekends

The dual testing approach ensures both specific examples work correctly (unit tests) and general behavior is verified across many inputs (property tests).