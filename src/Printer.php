<?php

namespace Skills17\PHPUnit;

use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\Test;
use PHPUnit\TextUI\DefaultResultPrinter;
use PHPUnit\Util\Test as UtilTest;
use Skills17\PHPUnit\Config;

class ResultPrinter extends DefaultResultPrinter
{
    private $results = [];
    private $ungroupedTests = [];
    private $groups;
    private $hasExtraTests = false;
    private $json = false;

    public function __construct(
        $out = null,
        bool $verbose = false,
        string $colors = self::COLOR_DEFAULT,
        bool $debug = false,
        $numberOfColumns = 80,
        bool $reverse = false
    ) {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);

        $this->groups = Config::getInstance()->getGroups();
        $this->json = Config::getInstance()->getFormat() === 'json';
    }

    /**
     * Print the result.
     */
    public function printResult(TestResult $result): void
    {
        if ($this->json) {
            $this->printFooter($result);
        } else {
            parent::printResult($result);
        }
    }

    /**
     * Show progress if format is not json.
     */
    protected function writeProgress(string $progress): void
    {
        if (!$this->json) {
            parent::writeProgress($progress);
        }
    }

    /**
     * Print the footer.
     */
    protected function printFooter(TestResult $result): void
    {
        $json = ['testResults' => [], 'warnings' => []];
        $missingExtraTests = [];
        $missingMainTests = [];
        $emptyGroups = [];

        if (!$this->json) {
            parent::printFooter($result);

            $this->writeNewLine();
            $this->writeWithColor(
                'fg-black, bg-green',
                '------------       RESULT       ------------'
            );
            $this->write("\nSummary:\n");
        }

        // print groups
        foreach ($this->groups as $groupIndex => $group) {
            $groupName = $group['displayName'] ?? $group['match'];

            // check if no tests were recorded for this group
            if (!isset($this->results[$groupIndex])) {
                $emptyGroups[] = $groupName;
                continue;
            }

            $maxGroupPoints = $this->calculateMaxGroupPoints($groupIndex);
            $scoredGroupPoints = $this->calculateScoredGroupPoints($groupIndex);
            $manualCheckRequired = $this->groupRequiresManualCheck($groupIndex);
            $groupJson = [
                'group' => $groupName,
                'points' => $scoredGroupPoints,
                'maxPoints' => $maxGroupPoints,
                'strategy' => $group['strategy'] ?? Config::getInstance()->getPointsStrategy(),
                'manualCheck' => $manualCheckRequired,
                'tests' => [],
            ];


            // Print the group header
            if (!$this->json) {
                $pointsText = $scoredGroupPoints . '/' . $maxGroupPoints . ' point' .
                    ($maxGroupPoints !== 1.0 ? 's' : '');

                $color = 'fg-red';
                if ($scoredGroupPoints === $maxGroupPoints) {
                    $color = 'fg-green';
                } elseif ($scoredGroupPoints > 0.0) { // partial
                    $color = 'fg-yellow';
                }

                $this->writeWithColor('bold', '  ' . $groupName . ': ', false);
                $this->writeWithColor($color, $pointsText, !$manualCheckRequired);

                if ($manualCheckRequired) {
                    $this->writeWithColor('fg-yellow, bold', ' [manual check required]');
                }
            }

            // print the tests
            foreach ($this->results[$groupIndex] as $testName => $result) {
                // check if an extra tests was executed without a main test
                if (!isset($result['main'])) {
                    $manualCheckRequired = true;
                    $missingMainTests[] = $groupName . ' > ' . $testName;
                    continue;
                }

                // check if a main test was executed without an extra test
                if (!isset($result['extra']) && $this->hasExtraTests) {
                    $missingExtraTests[] = $groupName . ' > ' . $testName;
                }

                if ($this->json) {
                    $groupJson['tests'][] = [
                        'name' => $testName,
                        'points' => $result['main']['status'] === true ? $result['main']['points'] : 0,
                        'maxPoints' => $result['main']['points'],
                        'failed' => $result['main']['status'] === false,
                        'manualCheck' => $result['main']['status'] === true && isset($result['extra']) &&
                            $result['extra']['status'] === false,
                    ];
                } else {
                    if ($result['main']['status'] === false) {
                        $resultText = 'failed';
                        $resultSymbol = '✗';
                        $resultColor = 'red';
                    } elseif (
                        $result['main']['status'] === true && (
                            !$this->hasExtraTests ||
                            !isset($result['extra']['status']) ||
                            $result['extra']['status'] === true
                        )
                    ) {
                        $resultText = 'ok';
                        $resultSymbol = '✓';
                        $resultColor = 'green';
                    } else {
                        $resultText = 'please check manually for static return values and/or logical errors';
                        $resultSymbol = '?';
                        $resultColor = 'yellow';
                    }

                    $this->write('    ');
                    $this->writeWithColor('fg-' . $resultColor . ', bold', $resultSymbol, false);
                    $this->write(' ' . $testName . ': ');

                    if ($resultColor === 'yellow') {
                        $this->writeWithColor('fg-yellow', 'WARNING: ', false);
                    }

                    $this->write($resultText);
                    $this->writeNewLine();
                }
            }

            if ($this->json) {
                $json['testResults'][] = $groupJson;
            }
        }

        // print info
        if (!$this->json) {
            $this->writeNewLine();
            $this->writeWithColor('fg-blue', 'Info: ', false);
            $this->write('The detailed test and error information is visible above the result summary.');
            $this->writeNewLine();
        }

        // print warnings: extra tests without main tests
        if ($this->hasExtraTests && count($missingMainTests) > 0) {
            $json['warnings'][] = $this->printTestWarnings(
                'The following extra tests do not belong to a main test and were ignored:',
                $missingMainTests
            );
        }

        // print warnings: main tests without extra tests
        if ($this->hasExtraTests && count($missingExtraTests) > 0) {
            $json['warnings'][] = $this->printTestWarnings(
                'The following tests do NOT have extra tests and so can NOT be checked for possible cheating:',
                $missingExtraTests
            );
        }

        // print warnings: tests without a group
        if (count($this->ungroupedTests) > 0) {
            $json['warnings'][] = $this->printTestWarnings(
                'The following tests do not belong to a group and were ignored:',
                $this->ungroupedTests
            );
        }

        // print warnings: groups without a test
        if (count($emptyGroups) > 0) {
            $json['warnings'][] = $this->printTestWarnings(
                'The following groups do not have any test:',
                $emptyGroups
            );
        }

        if ($this->json) {
            if (count($json['warnings']) === 0) {
                unset($json['warnings']);
            }

            $this->write(json_encode($json, JSON_PRETTY_PRINT));
        } else {
            $this->write("\n");
        }
    }

    /**
     * Save the test results together with some additional information.
     */
    public function endTest(Test $test, float $time): void
    {
        [$className, $testName] = UtilTest::describe($test);
        $isExtra = strpos($className, '\\Extra\\') !== false;
        $testInfo = $this->getTestInfo($testName);

        if ($testInfo === null) {
            $this->ungroupedTests[] = $className . '::' . $testName;
        } else {
            if ($isExtra) {
                $this->hasExtraTests = true;
            }

            if (!isset($this->results[$testInfo['groupIndex']])) {
                $this->results[$testInfo['groupIndex']] = [];
            }

            if (!isset($this->results[$testInfo['groupIndex']][$testName])) {
                $this->results[$testInfo['groupIndex']][$testName] = [];
            }

            $this->results[$testInfo['groupIndex']][$testName][$isExtra ? 'extra' : 'main'] = array_merge($testInfo, [
                'status' => !$this->lastTestFailed,
            ]);
        }

        parent::endTest($test, $time);
    }

    /**
     * Calculate the points scored within a group
     */
    private function calculateScoredGroupPoints(int $groupIndex): float
    {
        $group = $this->groups[$groupIndex];
        $strategy = $group['strategy'] ?? Config::getInstance()->getPointsStrategy();
        $points = $strategy === 'deduct' ? (float) $this->calculateMaxGroupPoints($groupIndex) : 0.0;

        foreach ($this->results[$groupIndex] as $result) {
            $testSuccessful = false;

            // check if the test was successful or not
            if (isset($result['main'])) {
                $testSuccessful = $result['main']['status'];
            }

            // add or deduct points based on the strategy
            if ($testSuccessful && $strategy === 'add') {
                $points += $result['main']['points'];
            } elseif (!$testSuccessful) {
                if ($strategy === 'deduct') {
                    $points -= $result['main']['points'];
                }

                if ($result['main']['required']) {
                    return 0;
                }
            }
        }

        return max($points, 0);
    }

    /**
     * Calculate the maximum possible points for a group.
     */
    private function calculateMaxGroupPoints(int $groupIndex): float
    {
        $group = $this->groups[$groupIndex];
        $strategy = $group['strategy'] ?? Config::getInstance()->getPointsStrategy();

        // respect the optional maxPoints value for the deduct strategy
        if ($strategy === 'deduct' && isset($group['maxPoints'])) {
            return $group['maxPoints'];
        }

        // add all possible points for the tests in the specified group
        return array_reduce($this->results[$groupIndex], function ($carry, $item) {
            if (isset($item['main'])) {
                return $carry + $item['main']['points'];
            }

            // add 0 points for extra tests without a main test
            return $carry + 0;
        }, 0.0);
    }

    /**
     * Check if a group requires a manual check
     */
    private function groupRequiresManualCheck(int $groupIndex): bool
    {
        foreach ($this->results[$groupIndex] as $result) {
            // check if a manual check is required
            if (isset($result['main']) && isset($result['extra'])) {
                if ($result['main']['status'] === true && $result['extra']['status'] === false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the group index of a test
     */
    private function getTestInfo(string $fullName): ?array
    {
        $name = substr($fullName, 0, 4) === 'test' ? substr($fullName, 4) : $fullName;

        // search the group in which the test is in
        foreach ($this->groups as $groupIndex => $group) {
            if (preg_match('/^' . $group['match'] . '$/', $name)) {
                $points = $group['defaultPoints'] ?? Config::getInstance()->getDefaultPoints();
                $required = false;

                // search for a specific test config
                foreach (($group['tests'] ?? []) as $test) {
                    if (preg_match('/^' . $test['match'] . '$/', $name)) {
                        $points = $test['points'] ?? $points;
                        $required = $test['required'] ?? $required;
                        break;
                    }
                }

                // return default values for no specific matches
                return [
                    'groupIndex' => $groupIndex,
                    'points' => $points,
                    'required' => false,
                ];
            }
        }

        return null;
    }

    /**
     * Prints a warning for a specific set of tests
     */
    private function printTestWarnings(string $warning, array $tests): string
    {
        if (!$this->json) {
            $this->writeNewLine();
            $this->writeWithColor(
                'fg-yellow',
                'WARNING: ' . $warning,
            );

            foreach ($tests as $test) {
                $this->write('  - ' . $test);
                $this->writeNewLine();
            }
        }

        return $warning . "\n  - " . implode("\n  - ", $tests);
    }
}
