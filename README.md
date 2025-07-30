# Cascading Camera System

A smart queue detection system for ski lifts that uses cascading camera activation to accurately count people waiting in line, filtering out those who are just hanging around in the waiting area.

## Overview

Traditional camera systems simply count all people in view, but this often includes people who aren't actually waiting for the lift (eating, adjusting equipment, socializing, etc.). The Cascading Camera System solves this by implementing dependency rules between cameras, where cameras only activate and contribute to the count when specific conditions are met by other cameras in the system.

## Core Functionality

### Camera Dependencies

Each camera can have an **activation rule** that determines when it should be considered active:

- **Base cameras** (no rule): Always active
- **Dependent cameras**: Only active when other cameras meet specific conditions

### Activation Rules

```php
// Always active (base camera)
$system->addCamera('DoorCam');

// Active when DoorCam has >= 1 person
$system->addCamera('RoomCam', ['camera' => 'DoorCam', 'minPeople' => 1]);

// Active when DoorCam has >= 3 people AND DoorCam is active
$system->addCamera('BackArea', [
    'camera' => 'DoorCam', 
    'minPeople' => 3, 
    'requireActive' => true
]);
```

### Algorithm

The system uses an iterative approach to resolve dependencies:

1. **Reset**: All cameras start as inactive
2. **Iterate**: Check each camera's activation rule
3. **Activate**: Turn on cameras that meet their conditions
4. **Repeat**: Continue until no more changes occur
5. **Count**: Sum people from all active cameras

**Complexity**: O(n × m) where n = number of cameras, m = max iterations

## Usage Example

```php
<?php
require_once 'CameraSystem.php';

// Create the system
$system = new CameraSystem();

// Add cameras with dependency rules
$system->addCamera('A');  // Near lift doors - always active
$system->addCamera('B', ['camera' => 'A', 'minPeople' => 1]);
$system->addCamera('C', ['camera' => 'A', 'minPeople' => 3]);
$system->addCamera('D', ['camera' => 'A', 'minPeople' => 3]);
$system->addCamera('E', ['camera' => 'B', 'minPeople' => 2, 'requireActive' => true]);
$system->addCamera('F');  // Rest area - always active

// Set current people counts from camera feeds
$system->setPeopleCount('A', 4);  // 4 people near doors
$system->setPeopleCount('B', 3);  // 3 people in area B
$system->setPeopleCount('C', 2);  // 2 people in area C
$system->setPeopleCount('D', 1);  // 1 person in area D
$system->setPeopleCount('E', 2);  // 2 people in back area
$system->setPeopleCount('F', 5);  // 5 people in rest area

// Calculate total people actually waiting
$totalWaiting = $system->calculateTotalPeople();
echo "People waiting for lift: $totalWaiting\n";

// Get breakdown of active cameras
$activeCameras = $system->getActiveCameras();
print_r($activeCameras);
```

## Key Benefits

- **Accurate Counting**: Filters out people not actually waiting
- **Flexible Rules**: Easy to configure complex dependency chains
- **Scalable**: Handles multiple cameras with various activation conditions
- **Real-time**: Efficiently processes camera updates

## Testing

Run the included test suite to verify functionality:

```bash
php CameraSystemTest.php
```

The tests cover:
- Basic functionality
- Complex dependency chains
- Edge cases and error handling
- Real-world ski lift scenarios

## Files

- `Camera.php` - Camera entity with people count and activation rules
- `CameraSystem.php` - Main system logic and algorithms
- `CameraSystemTest.php` - Comprehensive test suite with examples
