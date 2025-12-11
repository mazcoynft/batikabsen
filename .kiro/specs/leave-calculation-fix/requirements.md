# Requirements Document

## Introduction

This feature addresses the leave calculation logic in the employee leave request system. Currently, the system counts all days including weekends (Saturday and Sunday) when calculating the number of leave days. The requirement is to modify the calculation to exclude weekends, so only working days (Monday to Friday) are counted in leave requests.

## Glossary

- **Leave_System**: The employee leave request management system
- **Working_Days**: Monday through Friday (excluding weekends)
- **Weekend_Days**: Saturday and Sunday
- **Leave_Request**: An employee's formal request for time off
- **Leave_Calculation**: The process of determining the number of days for a leave request

## Requirements

### Requirement 1

**User Story:** As an employee, I want the system to calculate leave days excluding weekends, so that my leave balance is only reduced by actual working days.

#### Acceptance Criteria

1. WHEN an employee submits a leave request spanning multiple days, THE Leave_System SHALL calculate only working days (Monday to Friday)
2. WHEN a leave request includes Saturday or Sunday, THE Leave_System SHALL exclude these weekend days from the total count
3. WHEN calculating leave balance deduction, THE Leave_System SHALL use only the working days count
4. WHEN displaying leave duration to users, THE Leave_System SHALL show the working days count
5. WHEN a leave request spans only weekend days, THE Leave_System SHALL calculate zero working days

### Requirement 2

**User Story:** As an HR administrator, I want consistent leave calculations across all leave types, so that all leave requests follow the same working days calculation rule.

#### Acceptance Criteria

1. WHEN processing any type of leave request (sick leave, annual leave, personal leave), THE Leave_System SHALL apply working days calculation consistently
2. WHEN approving leave requests, THE Leave_System SHALL display the correct working days count to administrators
3. WHEN generating leave reports, THE Leave_System SHALL use working days calculations for all statistics

### Requirement 3

**User Story:** As a system administrator, I want the leave calculation to handle edge cases properly, so that the system works correctly in all scenarios.

#### Acceptance Criteria

1. WHEN a leave request spans across multiple weeks, THE Leave_System SHALL correctly exclude all weekend days within the range
2. WHEN a leave request starts or ends on a weekend, THE Leave_System SHALL handle the boundary conditions correctly
3. WHEN calculating leave for a single day that falls on a weekend, THE Leave_System SHALL return zero working days
4. WHEN a leave request spans exactly one work week (Monday to Friday), THE Leave_System SHALL calculate exactly 5 working days