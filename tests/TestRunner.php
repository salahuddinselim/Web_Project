<?php
/**
 * Simple Test Runner for Pranayom Fitness Management System
 */

define('TEST_BASE_DIR', __DIR__);
require_once __DIR__ . '/../config/database.php';

$tests = [];
foreach (glob(__DIR__ . '/*Test.php') as $filename) {
    require_once $filename;
    $className = basename($filename, '.php');
    if (class_exists($className)) {
        $tests[] = new $className();
    }
}

$passed = 0;
$failed = 0;
$results = [];

echo "\n🚀 Starting Pranayom Test Suite\n";
echo "================================\n\n";

foreach ($tests as $test) {
    $className = get_class($test);
    echo "Running $className:\n";
    
    $methods = get_class_methods($test);
    foreach ($methods as $method) {
        if (strpos($method, 'test') === 0) {
            try {
                $test->$method();
                echo "  ✅ $method passed\n";
                $passed++;
            } catch (Exception $e) {
                echo "  ❌ $method FAILED: " . $e->getMessage() . "\n";
                $failed++;
                $results[] = "$className::$method: " . $e->getMessage();
            }
        }
    }
    echo "\n";
}

echo "================================\n";
echo "Summary: $passed Passed, $failed Failed\n";

if ($failed > 0) {
    echo "\nFailures:\n";
    foreach ($results as $res) {
        echo "- $res\n";
    }
    exit(1);
} else {
    echo "\n✅ All tests passed!\n";
    exit(0);
}

/**
 * Basic Assertion Helper
 */
function assertEqual($expected, $actual, $message = "") {
    if ($expected !== $actual) {
        throw new Exception($message . " (Expected: " . var_export($expected, true) . ", Actual: " . var_export($actual, true) . ")");
    }
}

function assertNotEmpty($actual, $message = "") {
    if (empty($actual)) {
        throw new Exception($message . " (Expected non-empty, but got empty)");
    }
}
