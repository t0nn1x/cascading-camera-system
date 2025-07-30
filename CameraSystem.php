<?php

require_once 'Camera.php';

/**
 * Cascading Camera System for counting people waiting for a ski lift
 * 
 * This system implements a smart counting algorithm where cameras are activated
 * in a cascade manner based on people count in other cameras. This helps distinguish
 * between people actually waiting for the lift vs. people just hanging around
 * (eating, dressing up, etc.) in the waiting area.
 * 
 * Algorithm complexity: O(n * m) where n = number of cameras, m = max iterations
 * for dependency resolution. In practice, close to O(n) for simple cases.
 * Space complexity: O(n) for storing cameras and their states.
 */
class CameraSystem {

    /**
     * Array of cameras in the system
     */
    private array $cameras = [];

    /**
     * Adds a camera to the system with optional activation rule
     * 
     * @param string $name Camera identifier/name
     * @param array $rule Activation rule array:
     *                   [] - always active (no dependencies)
     *                   ['camera' => 'A', 'minPeople' => 1] - active if camera A has >= 1 people
     *                   ['camera' => 'A', 'minPeople' => 1, 'requireActive' => true] - active if camera A has >= 1 people AND camera A is active
     * 
     * @return void
     * 
     * @example
     * $system->addCamera('DoorCam'); // Always active
     * $system->addCamera('RoomCam', ['camera' => 'DoorCam', 'minPeople' => 1]); // Active when DoorCam has >= 1 person
     */
    public function addCamera(string $name, array $rule = []): void { 
        $this->cameras[$name] = new Camera($name, 0, $rule);
    }

    /**
     * Sets the number of people detected by a specific camera
     * 
     * @param string $cameraName Name of the camera to update
     * @param int $count Number of people detected (must be >= 0)
     * 
     * @throws InvalidArgumentException If camera with given name doesn't exist
     * 
     * @return void
     */
    public function setPeopleCount(string $cameraName, int $count): void {
        if (!isset($this->cameras[$cameraName])) {
            throw new InvalidArgumentException("Camera '$cameraName' not found");
        }

        $this->cameras[$cameraName]->peopleCount = $count;
    }

    /**
     * Checks if a camera should be activated based on its activation rule
     * 
     * This method evaluates the camera's dependency rule against the current
     * state of other cameras in the system.
     * 
     * @param Camera $camera The camera to check activation for
     * 
     * @return bool True if the camera should be activated, false otherwise
     * 
     * @throws InvalidArgumentException If dependent camera referenced in rule doesn't exist
     * 
     * @example
     * // For a camera with rule ['camera' => 'A', 'minPeople' => 3, 'requireActive' => true]
     * // Returns true only if camera A has >= 3 people AND camera A is currently active
     */
    public function checkActivationRule(Camera $camera): bool { 
        if (empty($camera->activationRule)) {
            return true;
        }

        $rule = $camera->activationRule;
        $dependentCameraName = $rule['camera'];
        $minPeople = $rule['minPeople'] ?? 0;
        $requireActive = $rule['requireActive'] ?? false;

        if (!isset($this->cameras[$dependentCameraName])) {
            throw new InvalidArgumentException("Dependent camera '$dependentCameraName' not found");
        }

        $dependentCamera = $this->cameras[$dependentCameraName];

        $hasEnoughPeople = $dependentCamera->peopleCount >= $minPeople;
        $isActiveIfRequired = !$requireActive || $dependentCamera->isActive; 

        return $hasEnoughPeople && $isActiveIfRequired; // true if the camera should be activated, false otherwise
    }

    /**
     * Main algorithm: Calculates total number of people waiting for the lift
     * 
     * This method uses an iterative approach to resolve camera dependencies.
     * It runs multiple passes until no more cameras can be activated, ensuring
     * all cascade dependencies are properly resolved.
     * 
     * Process:
     * 1. Reset all cameras to inactive state
     * 2. Iteratively check each camera's activation rule
     * 3. Activate cameras that meet their conditions
     * 4. Repeat until no changes occur or max iterations reached
     * 5. Sum people count from all active cameras
     * 
     * @return int Total number of people from active cameras
     * 
     * @example
     * // Setup system
     * $system->addCamera('A'); // Always active
     * $system->addCamera('B', ['camera' => 'A', 'minPeople' => 1]);
     * $system->setPeopleCount('A', 2);
     * $system->setPeopleCount('B', 5);
     * 
     * $total = $system->calculateTotalPeople(); // Returns 7 (2 + 5)
     */
    public function calculateTotalPeople(): int {
        foreach($this->cameras as $camera) {
            $camera->isActive = false;
        }

        $maxIteration = count($this->cameras);
        $changed = true;
        $iteration = 0;

        while ($changed && $iteration < $maxIteration) {
            $changed = false;
            $iteration++;
            
            foreach ($this->cameras as $camera) {
                if ($camera->isActive) {
                    continue;
                }

                if ($this->checkActivationRule($camera)) {
                    $camera->isActive = true;
                    $changed = true;
                }
            }
        }

        $total = 0;
        foreach ($this->cameras as $camera) {
            if ($camera->isActive) {
                $total += $camera->peopleCount;
            }
        }

        return $total;
    }
}
