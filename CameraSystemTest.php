<?php

require_once 'CameraSystem.php';

class CameraSystemTest
{
    private int $testsPassed = 0;
    private int $testsTotal = 0;

    public function runAllTests(): void
    {
        echo "Running camera system tests\n";
        echo str_repeat("=", 50) . "\n";

        $this->testBasicFunctionality();
        $this->testOriginalExample();
        $this->testEdgeCases();

        echo str_repeat("=", 50) . "\n";
        echo "✅ Tests passed: {$this->testsPassed}/{$this->testsTotal}\n";

        if ($this->testsPassed === $this->testsTotal) {
            echo "All tests passed successfully!\n";
        } else {
            echo "❌ Some tests failed!\n";
        }
    }

    private function testBasicFunctionality(): void
    {
        echo "Test 1: Basic functionality\n";

        $system = new CameraSystem();
        $system->addCamera('A'); // Always active
        $system->addCamera('B', ['camera' => 'A', 'minPeople' => 1]);

        // Test 1.1: A=0, B=5
        $system->setPeopleCount('A', 0);
        $system->setPeopleCount('B', 5);
        $result = $system->calculateTotalPeople();
        $this->assertEquals(0, $result, "A=0, B=5 should give 0");

        // Test 1.2: A=1, B=5  
        $system->setPeopleCount('A', 1);
        $result = $system->calculateTotalPeople();
        $this->assertEquals(6, $result, "A=1, B=5 should give 6");
    }

    private function testOriginalExample(): void
    {
        echo "Test 2: Original example from task\n";

        $system = new CameraSystem();
        $system->addCamera('A'); // Always active
        $system->addCamera('B', ['camera' => 'A', 'minPeople' => 1]);
        $system->addCamera('C', ['camera' => 'A', 'minPeople' => 3]);
        $system->addCamera('D', ['camera' => 'A', 'minPeople' => 3]);
        $system->addCamera('E', ['camera' => 'B', 'minPeople' => 2, 'requireActive' => true]);
        $system->addCamera('F'); // Always active

        // Example 1: A=0, B=5, C=3, D=2, E=4, F=7
        $system->setPeopleCount('A', 0);
        $system->setPeopleCount('B', 5);
        $system->setPeopleCount('C', 3);
        $system->setPeopleCount('D', 2);
        $system->setPeopleCount('E', 4);
        $system->setPeopleCount('F', 7);

        $result = $system->calculateTotalPeople();
        $this->assertEquals(7, $result, "Example 1 should give 7 (only A=0 and F=7)");

        // Example 2: A=4, B=3, C=3, D=2, E=1, F=7
        $system->setPeopleCount('A', 4);
        $system->setPeopleCount('B', 3);
        $system->setPeopleCount('C', 3);
        $system->setPeopleCount('D', 2);
        $system->setPeopleCount('E', 1);
        $system->setPeopleCount('F', 7);

        $result = $system->calculateTotalPeople();
        $expected = 4 + 3 + 3 + 2 + 1 + 7; // All cameras active
        $this->assertEquals($expected, $result, "Example 2 should give $expected");
    }

    private function testEdgeCases(): void
    {
        echo "Test 3: Edge cases\n";

        $system = new CameraSystem();
        $system->addCamera('EMPTY'); // Always active, but 0 people

        $system->setPeopleCount('EMPTY', 0);
        $result = $system->calculateTotalPeople();
        $this->assertEquals(0, $result, "Camera with 0 people should give 0");

        // Test with non-existent camera
        try {
            $system->setPeopleCount('NONEXISTENT', 5);
            $this->fail("Should throw exception for non-existent camera");
        } catch (InvalidArgumentException $e) {
            $this->pass("Correctly threw exception for non-existent camera");
        }
    }

    private function assertEquals($expected, $actual, string $message): void
    {
        $this->testsTotal++;
        if ($expected === $actual) {
            echo "   ✅ $message\n";
            $this->testsPassed++;
        } else {
            echo "   ❌ $message (expected: $expected, actual: $actual)\n";
        }
    }

    private function pass(string $message): void
    {
        $this->testsTotal++;
        $this->testsPassed++;
        echo "   ✅ $message\n";
    }

    private function fail(string $message): void
    {
        $this->testsTotal++;
        echo "   ❌ $message\n";
    }
}

// ==============================================
// DEMO SECTION - SKI RESORT USAGE EXAMPLE
// ==============================================

function runSkiResortDemo(): void
{
    echo "\nCamera system for ski resort - Demo\n";
    echo str_repeat("=", 50) . "\n";

    // Create system according to the original task example
    $skiSystem = new CameraSystem();
    $skiSystem->addCamera('A'); // Near the lift doors
    $skiSystem->addCamera('B', ['camera' => 'A', 'minPeople' => 1]);
    $skiSystem->addCamera('C', ['camera' => 'A', 'minPeople' => 3]);
    $skiSystem->addCamera('D', ['camera' => 'A', 'minPeople' => 3]);
    $skiSystem->addCamera('E', ['camera' => 'B', 'minPeople' => 2, 'requireActive' => true]);
    $skiSystem->addCamera('F'); // Rest area (always counted)

    // Demo scenario: Different people counts
    $skiSystem->setPeopleCount('A', 4);
    $skiSystem->setPeopleCount('B', 3);
    $skiSystem->setPeopleCount('C', 2);
    $skiSystem->setPeopleCount('D', 1);
    $skiSystem->setPeopleCount('E', 2);
    $skiSystem->setPeopleCount('F', 5);

    $total = $skiSystem->calculateTotalPeople();
    $activeCameras = $skiSystem->getActiveCameras();

    echo "Demo scenario:\n";
    echo "Camera A (lift doors): 4 people\n";
    echo "Camera B (hallway): 3 people\n";
    echo "Camera C (room area 1): 2 people\n";
    echo "Camera D (room area 2): 1 people\n";
    echo "Camera E (secondary area): 2 people\n";
    echo "Camera F (rest area): 5 people\n\n";
    
    echo "Result:\n";
    echo "Total number of people in queue: $total\n";
    echo "Active cameras: " . json_encode($activeCameras) . "\n";
}

runSkiResortDemo();

echo "\n";
$tester = new CameraSystemTest();
$tester->runAllTests();
