<?php

class Camera {
    public string $name;
    public int $peopleCount;
    public array $activationRule;
    public bool $isActive = false;

    public function __construct(string $name, int $peopleCount = 0, array $activationRule = []) {
        $this->name = $name;
        $this->peopleCount = $peopleCount;
        $this->activationRule = $activationRule;
    }
}
