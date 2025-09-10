<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Chevere\Action\Exceptions\ActionException;
use Chevere\Tests\src\ActionTestAssertArgumentsDefinedVars;
use Chevere\Tests\src\ActionTestAssertArgumentsExplicit;
use Chevere\Tests\src\ActionTestAssertArgumentsImplicit;

function benchmarkTest(callable $testFunction, string $testName, int $iterations = 1000): array
{
    $times = [];

    for ($i = 0; $i < $iterations; $i++) {
        $start = hrtime(true);

        try {
            $testFunction();
        } catch (ActionException $e) {
            // Expected exception, ignore
        }

        $end = hrtime(true);
        $times[] = ($end - $start) / 1000000; // Convert to milliseconds
    }

    $avgTime = array_sum($times) / count($times);
    $minTime = min($times);
    $maxTime = max($times);

    return [
        'test_name' => $testName,
        'iterations' => $iterations,
        'avg_time_ms' => $avgTime,
        'min_time_ms' => $minTime,
        'max_time_ms' => $maxTime,
        'total_time_ms' => array_sum($times),
    ];
}

function testAssertArgumentsExplicit(): void
{
    $action = new ActionTestAssertArgumentsExplicit();
    $action->__invoke('error', -111);
}

function testAssertArgumentsImplicit(): void
{
    $action = new ActionTestAssertArgumentsImplicit();
    $action->__invoke('error', -111);
}

function testAssertArgumentsDefinedVars(): void
{
    $action = new ActionTestAssertArgumentsDefinedVars();
    $action->__invoke('error', -111);
}

// Warm up
echo "Warming up...\n";
for ($i = 0; $i < 100; $i++) {
    try {
        testAssertArgumentsExplicit();
    } catch (ActionException $e) {
    }

    try {
        testAssertArgumentsImplicit();
    } catch (ActionException $e) {
    }

    try {
        testAssertArgumentsDefinedVars();
    } catch (ActionException $e) {
    }
}

echo "Running benchmarks...\n\n";

$iterations = 1000;

// Benchmark all tests
$explicitResults = benchmarkTest('testAssertArgumentsExplicit', 'testAssertArgumentsExplicit', $iterations);
$implicitResults = benchmarkTest('testAssertArgumentsImplicit', 'testAssertArgumentsImplicit', $iterations);
$definedVarsResults = benchmarkTest('testAssertArgumentsDefinedVars', 'testAssertArgumentsDefinedVars', $iterations);

// Display results
echo "=== BENCHMARK RESULTS ===\n\n";

echo "Test: {$explicitResults['test_name']}\n";
echo "Iterations: {$explicitResults['iterations']}\n";
echo 'Average time: ' . number_format($explicitResults['avg_time_ms'], 4) . " ms\n";
echo 'Min time: ' . number_format($explicitResults['min_time_ms'], 4) . " ms\n";
echo 'Max time: ' . number_format($explicitResults['max_time_ms'], 4) . " ms\n";
echo 'Total time: ' . number_format($explicitResults['total_time_ms'], 2) . " ms\n\n";

echo "Test: {$implicitResults['test_name']}\n";
echo "Iterations: {$implicitResults['iterations']}\n";
echo 'Average time: ' . number_format($implicitResults['avg_time_ms'], 4) . " ms\n";
echo 'Min time: ' . number_format($implicitResults['min_time_ms'], 4) . " ms\n";
echo 'Max time: ' . number_format($implicitResults['max_time_ms'], 4) . " ms\n";
echo 'Total time: ' . number_format($implicitResults['total_time_ms'], 2) . " ms\n\n";

echo "Test: {$definedVarsResults['test_name']}\n";
echo "Iterations: {$definedVarsResults['iterations']}\n";
echo 'Average time: ' . number_format($definedVarsResults['avg_time_ms'], 4) . " ms\n";
echo 'Min time: ' . number_format($definedVarsResults['min_time_ms'], 4) . " ms\n";
echo 'Max time: ' . number_format($definedVarsResults['max_time_ms'], 4) . " ms\n";
echo 'Total time: ' . number_format($definedVarsResults['total_time_ms'], 2) . " ms\n\n";

// Compare results
$explicitAvg = $explicitResults['avg_time_ms'];
$implicitAvg = $implicitResults['avg_time_ms'];
$definedVarsAvg = $definedVarsResults['avg_time_ms'];

echo "=== COMPARISON ===\n";

// Find the fastest test
$allResults = [
    'Explicit' => $explicitAvg,
    'Implicit' => $implicitAvg,
    'DefinedVars' => $definedVarsAvg,
];

$fastest = array_keys($allResults, min($allResults))[0];
$slowest = array_keys($allResults, max($allResults))[0];

echo "Fastest: testAssertArguments{$fastest} ({$allResults[$fastest]} ms)\n";
echo "Slowest: testAssertArguments{$slowest} ({$allResults[$slowest]} ms)\n";

$fastestTime = min($allResults);
$slowestTime = max($allResults);
$diff = (($slowestTime - $fastestTime) / $fastestTime) * 100;

echo 'Performance difference: ' . number_format($diff, 2) . "%\n";
echo 'Time difference: ' . number_format($slowestTime - $fastestTime, 4) . " ms\n";
