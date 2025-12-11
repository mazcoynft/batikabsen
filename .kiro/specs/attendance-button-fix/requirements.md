# Requirements Document

## Introduction

This specification addresses critical issues with the attendance system's button display logic and alert behavior. The system currently has problems where:
1. The "Belum Waktunya Pulang" alert shows but buttons remain as "Hadir", "Onsite", "WFH" instead of showing "Pulang" button
2. The alert requires manual dismissal instead of auto-disappearing
3. After successful attendance submission, the system should redirect to dashboard and show appropriate buttons on return

## Glossary

- **Attendance System**: The mobile frontend attendance capture system
- **Clock-in**: The process of recording arrival time (absen masuk)
- **Clock-out**: The process of recording departure time (absen pulang)
- **Button State**: The current display state of attendance action buttons
- **Alert Behavior**: How system notifications are displayed and dismissed

## Requirements

### Requirement 1

**User Story:** As an employee who has already clocked in, I want to see only the appropriate "Pulang" button when I access the attendance page, so that I can easily clock out when it's time.

#### Acceptance Criteria

1. WHEN a user has clocked in but not clocked out THEN the system SHALL display only the appropriate "Pulang" button based on their clock-in type
2. WHEN the clock-in type is normal attendance THEN the system SHALL display "Pulang" button
3. WHEN the clock-in type is WFH THEN the system SHALL display "Pulang WFH" button
4. WHEN the clock-in type is Onsite THEN the system SHALL display "Pulang Onsite" button
5. WHEN a user has not clocked in yet THEN the system SHALL display "Hadir", "Onsite", and "WFH" buttons

### Requirement 2

**User Story:** As an employee trying to clock out before the allowed time, I want to see a brief auto-dismissing alert, so that I understand the restriction without needing to manually close the alert.

#### Acceptance Criteria

1. WHEN a user clicks "Pulang" before the allowed time THEN the system SHALL display a warning alert
2. WHEN the early clock-out alert is displayed THEN the system SHALL auto-dismiss the alert after 1 second
3. WHEN the early clock-out alert is displayed THEN the system SHALL NOT require manual dismissal
4. WHEN the early clock-out alert is displayed THEN the system SHALL show the exact allowed clock-out time
5. WHEN the current time is after the allowed clock-out time THEN the system SHALL allow normal clock-out processing

### Requirement 3

**User Story:** As an employee who completes attendance actions, I want the system to redirect me to the dashboard and maintain proper state, so that subsequent visits show the correct buttons.

#### Acceptance Criteria

1. WHEN a user successfully clocks in THEN the system SHALL redirect to the dashboard
2. WHEN a user successfully clocks out THEN the system SHALL redirect to the dashboard
3. WHEN a user returns to the attendance page after clocking in THEN the system SHALL display the appropriate "Pulang" button
4. WHEN a user returns to the attendance page after completing both clock-in and clock-out THEN the system SHALL display completion message
5. WHEN attendance data is submitted THEN the system SHALL update all related dashboard components (attendance cards, history, monthly stats, leaderboard)

### Requirement 4

**User Story:** As an employee using different attendance types, I want the system to maintain the correct button state and functionality for each type, so that I can clock out using the same method I used to clock in.

#### Acceptance Criteria

1. WHEN a user clocks in using "Hadir" THEN subsequent visits SHALL show "Pulang" button
2. WHEN a user clocks in using "WFH" THEN subsequent visits SHALL show "Pulang WFH" button
3. WHEN a user clocks in using "Onsite" THEN subsequent visits SHALL show "Pulang Onsite" button
4. WHEN a user clicks any "Pulang" button THEN the system SHALL use the appropriate attendance processing logic
5. WHEN attendance is processed THEN the system SHALL maintain data integrity across all system components