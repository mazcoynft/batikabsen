# Implementation Plan

- [x] 1. Create WorkingDaysCalculator service


  - Create new service class in `app/Services/WorkingDaysCalculator.php`
  - Implement `calculateWorkingDays()` method to count only Monday-Friday
  - Implement `isWeekend()` helper method to check if date is Saturday/Sunday
  - Implement `getWorkingDaysInRange()` method to return collection of working days
  - _Requirements: 1.1, 1.2, 3.1_



- [x] 1.1 Write property test for working days calculation

  - **Property 1: Working days calculation excludes weekends**
  - **Validates: Requirements 1.1, 1.2, 3.1**

- [ ] 1.2 Write unit tests for WorkingDaysCalculator service
  - Test specific date range examples (Monday-Friday, weekend-only, mixed ranges)


  - Test edge cases (same day, invalid ranges, single weekend days)
  - Test boundary conditions (ranges starting/ending on weekends)
  - _Requirements: 1.1, 1.2, 3.1, 3.2, 3.3, 3.4_

- [x] 2. Update IzinController to use working days calculation


  - Modify `store()` method in `IzinController.php`
  - Replace simple date difference with WorkingDaysCalculator service

  - Update leave balance calculation logic to use working days count
  - Ensure all leave types use the new calculation method
  - _Requirements: 1.1, 1.3, 2.1_


- [ ] 2.1 Write property test for leave balance deduction
  - **Property 2: Leave balance deduction matches working days**
  - **Validates: Requirements 1.3**


- [ ] 2.2 Write property test for consistent calculation across leave types
  - **Property 3: Consistent calculation across leave types**
  - **Validates: Requirements 2.1, 2.3**


- [ ] 3. Update frontend display logic
  - Modify leave request form to show working days count


  - Update leave history display to show working days
  - Ensure admin interfaces display correct working days count
  - _Requirements: 1.4, 2.2_



- [x] 3.1 Write property test for UI display consistency


  - **Property 4: UI display consistency**
  - **Validates: Requirements 1.4, 2.2**

- [ ] 3.2 Write unit test for one work week calculation
  - **Property 5: One work week equals five days**



  - **Validates: Requirements 3.4**

- [ ] 4. Update any existing leave reports or statistics
  - Review and update leave reporting logic to use working days
  - Ensure leave statistics calculations use working days count
  - Update any dashboard widgets that display leave information
  - _Requirements: 2.3_

- [ ] 5. Checkpoint - Make sure all tests are passing
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 6. Test integration with existing leave request flow
  - Verify leave request submission works with new calculation
  - Test leave approval process with working days display
  - Verify leave balance updates correctly with working days deduction
  - Test edge cases like weekend-only leave requests
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 7. Final Checkpoint - Make sure all tests are passing
  - Ensure all tests pass, ask the user if questions arise.