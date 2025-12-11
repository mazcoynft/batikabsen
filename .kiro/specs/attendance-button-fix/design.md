# Design Document

## Overview

This design addresses critical issues in the attendance system's frontend logic, specifically focusing on proper button state management and alert behavior. The system needs to correctly display attendance buttons based on the user's current attendance status and provide a smooth user experience with auto-dismissing alerts.

## Architecture

The attendance system follows a client-server architecture where:
- Frontend (Blade template with JavaScript) handles UI state and user interactions
- Backend API processes attendance data and returns current status
- State synchronization occurs through API calls and localStorage

## Components and Interfaces

### Frontend Components

1. **Button State Manager**
   - Manages display of attendance buttons based on current status
   - Handles transitions between "masuk" and "pulang" states
   - Maintains consistency with server-side data

2. **Alert System**
   - Displays time-based restrictions with auto-dismiss functionality
   - Provides user feedback without requiring manual interaction
   - Shows contextual information about attendance rules

3. **Attendance API Client**
   - Handles communication with backend attendance endpoints
   - Processes responses and updates UI state accordingly
   - Manages error handling and user feedback

### Backend Interfaces

1. **Attendance Status Endpoint**
   - Returns current attendance status for the user
   - Provides attendance type information (normal, WFH, onsite)
   - Includes timing information for validation

2. **Attendance Submission Endpoint**
   - Processes attendance data (clock-in/clock-out)
   - Updates all related system components
   - Returns success/failure status with appropriate messages

## Data Models

### Attendance State
```javascript
{
  isAbsenMasuk: boolean,        // true for clock-in, false for clock-out
  jenisPresensi: string,        // 'biasa', 'wfh', 'onsite'
  jamIn: string,                // clock-in time if exists
  jamOut: string,               // clock-out time if exists
  jamPulang: string,            // allowed clock-out time
  isCompleted: boolean          // both clock-in and clock-out done
}
```

### Button Configuration
```javascript
{
  type: string,                 // 'masuk' or 'pulang'
  buttons: [
    {
      id: string,
      text: string,
      className: string,
      icon: string,
      handler: function
    }
  ]
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*
Property 1: Button state reflects attendance status
*For any* attendance status (clocked-in, not clocked-in, completed), the displayed buttons should correctly match the current state
**Validates: Requirements 1.1, 3.3**

Property 2: Time validation controls clock-out access
*For any* current time and allowed clock-out time, the system should only allow clock-out when current time is at or after the allowed time
**Validates: Requirements 2.1, 2.5**

Property 3: Alert auto-dismissal behavior
*For any* early clock-out attempt, the warning alert should auto-dismiss after exactly 1 second without requiring user interaction
**Validates: Requirements 2.2, 2.3**

Property 4: Alert content accuracy
*For any* early clock-out alert, the displayed time should exactly match the configured allowed clock-out time
**Validates: Requirements 2.4**

Property 5: Successful attendance redirects to dashboard
*For any* successful attendance submission (clock-in or clock-out), the system should redirect to the dashboard page
**Validates: Requirements 3.1, 3.2**

Property 6: Attendance type consistency
*For any* attendance type used for clock-in, the subsequent "Pulang" button should match the same type
**Validates: Requirements 4.1, 4.2, 4.3, 4.4**

## Error Handling

### Time Validation Errors
- Early clock-out attempts show auto-dismissing alerts
- Missing time configuration shows appropriate warnings
- Invalid time formats are handled gracefully

### Network Errors
- Failed API calls show user-friendly error messages
- Retry mechanisms for transient failures
- Offline state handling with appropriate messaging

### State Synchronization Errors
- Mismatched client-server state triggers data refresh
- Invalid attendance states show error messages
- Corrupted localStorage data is reset to defaults

## Testing Strategy

### Unit Testing
- Button state management functions
- Time validation logic
- Alert display and dismissal behavior
- API request/response handling

### Property-Based Testing
- Use Jest with property-based testing library
- Generate random attendance states and verify correct button display
- Test time validation across various time combinations
- Verify alert behavior consistency across different scenarios
- Each property-based test should run minimum 100 iterations
- Property-based tests must reference design document properties using format: '**Feature: attendance-button-fix, Property {number}: {property_text}**'

### Integration Testing
- End-to-end attendance flow testing
- Dashboard component update verification
- Cross-browser compatibility testing
- Mobile device testing for touch interactions