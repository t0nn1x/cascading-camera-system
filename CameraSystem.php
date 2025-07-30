<?php

require_once 'Camera.php';

// ['camera' => 'A', 'minPeople' => 1] - active if A has >= 1 people
// ['camera' => 'A', 'minPeople' => 1, 'requireActive' => true] - active if A has >= 1 people and A should be active
class CameraSystem {
    private array $cameras = [];

    public function addCamera(string $name, array $rule = []): void { 
        $this->cameras[$name] = new Camera($name, 0, $rule);
    }

    public function setPeopleCount(string $cameraName, int $count): void {
        if (!isset($this->cameras[$cameraName])) {
            throw new InvalidArgumentException("Camera '$cameraName' not found");
        }

        $this->cameras[$cameraName]->peopleCount = $count;
    }

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
