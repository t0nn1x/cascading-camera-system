<?php

/**
 * Camera Entity for Ski Lift Queue Detection System
 * 
 * Represents a single camera in the cascading camera system. Each camera
 * can detect a certain number of people and has an activation rule that
 * determines when it should be considered active for counting purposes.
 * 
 * This is a simple data model class that holds camera state and configuration.
 * The activation logic is handled by the CameraSystem class.
 * 
 * 
 * @see CameraSystem For the main logic that uses this class
 */
class Camera {
    public string $name;
    public int $peopleCount;
    /**
     * * Rule format:
     * - [] (empty array) = always active (base camera)
     * - ['camera' => 'A', 'minPeople' => 1] = active if camera A has >= 1 people
     * - ['camera' => 'A', 'minPeople' => 3, 'requireActive' => true] = active if camera A has >= 3 people AND camera A is active
     */
    public array $activationRule;
    public bool $isActive = false;

    public function __construct(string $name, int $peopleCount = 0, array $activationRule = []) {
        $this->name = $name;
        $this->peopleCount = $peopleCount;
        $this->activationRule = $activationRule;
    }
}
