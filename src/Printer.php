<?php

namespace Skills17\PHPUnit;

use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\Test;
use PHPUnit\TextUI\DefaultResultPrinter;
use Skills17\PHPUnit\Config;

class Trade17Printer extends DefaultResultPrinter
{
    private $results = [];
    private $criteria;
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

        $config = Config::get();
        $this->criteria = $config['criteria'] ?? [];
        $this->criteria['unknown'] = ['points' => 0];
        $this->json = $config['format'] === 'json';
    }

    public function printResult(TestResult $result): void
    {
        if ($this->json) {
            $this->printFooter($result);
        } else {
            parent::printResult($result);
        }
    }

    protected function writeProgress(string $progress): void
    {
        if (!$this->json) {
            parent::writeProgress($progress);
        }
    }

    protected function printFooter(TestResult $result): void
    {
        $missingExtraTests = [];
        $missingMainTests = [];

        if ($this->json) {
            $this->write("{\n");
        } else {
            parent::printFooter($result);

            $this->write("\n\n");
            $this->writeWithColor(
                'fg-black, bg-green',
                '------------       RESULT       ------------'
            );
            $this->write("\nSummary:\n");
        }

        // Attribute a status to each test and print its result
        foreach ($this->results as $key => $tests) { // $key: ID of the group of tests
            $manualCheckRequired = false;
            $points = 0;
            $maxPoints = 0;

            foreach ($tests as $method => $result) {
                $testPoints = $this->criteria[$key]['pointOverrides'][$method] ?? 1;

                $maxPoints += $testPoints;

                // check if an extra tests was executed without a main test
                if (!isset($result['main'])) {
                    $manualCheckRequired = true;
                    $missingMainTests[] = $method;
                    continue;
                }

                // check if a main test was executed without an extra test
                if (!isset($result['extra']) && $this->hasExtraTests) {
                    $missingExtraTests[] = $method;
                }

                // check if extra test failed while main test succeeded
                if ($result['main'] === true && isset($result['extra']) && $result['extra'] === false) {
                    $manualCheckRequired = true;
                    continue;
                }

                // Add points when the test passes
                if ($result['main'] !== false) {
                    $points += $testPoints;
                }
            }

            // Print the group header
            if ($this->json) {
                $this->write('    "' . $key . '": ');
                $this->write(!$manualCheckRequired ? $points : '"manual_check"');
            } else {
                $pointsText = "$points/$maxPoints point" . ($points !== 1 ? 's' : '');

                $color = 'fg-red';
                if ($points === $maxPoints) {
                    $color = 'fg-green';
                } elseif ($points > 0.0) { // partial
                    $color = 'fg-blue';
                }

                if ($manualCheckRequired) {
                    $color = 'fg-yellow, bold';
                    $pointsText = 'manual check required';
                }

                $testName = $this->criteria[$key]['displayName'] ?? $key;
                $this->writeWithColor('bold', '  ' . $testName . ': ', false);

                $this->writeWithColor($color, $pointsText);
            }

            // Print individual tests
            if (!$this->json) {
                foreach ($tests as $method => $result) {
                    $testPoints = $this->criteria[$key]['pointOverrides'][$method] ?? 1;

                    if ($result['main'] === false) {
                        $resultText = 'failed';
                        $resultSymbol = '✗';
                        $resultColor = 'red';
                    } elseif (
                        $result['main'] === true &&
                        (!$this->hasExtraTests || !isset($result['extra']) || $result['extra'] === true)
                    ) {
                        $resultText = 'ok';
                        $resultSymbol = '✓';
                        $resultColor = 'green';
                    } else {
                        $resultText = 'WARNING: please check manually for static return values and/or logical errors';
                        $resultSymbol = '?';
                        $resultColor = 'yellow';
                    }

                    $this->write('    ');
                    $this->writeWithColor("fg-$resultColor, bold", $resultSymbol, false);
                    $this->write(str_pad(" [$testPoints] ", 5) . "$method: $resultText\n");
                }
            }

            if ($this->json) {
                if (next($this->results) !== false) {
                    $this->write(',');
                }

                $this->write("\n");
            }
        }

        if ($this->json) {
            $this->write('}');
        }

        if ($this->hasExtraTests && count($missingMainTests) > 0) {
            $this->write("\n\n\n");
            $this->writeWithColor(
                'fg-yellow',
                'WARNING: the following extra tests do not belong to a main test and were ignored:'
            );

            foreach ($missingMainTests as $missingMainTest) {
                $this->write('  - ' . $missingMainTest . "\n");
            }
        }

        if ($this->hasExtraTests && count($missingExtraTests) > 0) {
            $this->write("\n\n\n");
            $this->writeWithColor(
                'fg-yellow',
                'WARNING: the following tests do NOT have extra tests and so can NOT be checked for possible cheating:'
            );

            foreach ($missingExtraTests as $missingExtraTest) {
                $this->write('  - ' . $missingExtraTest . "\n");
            }
        }

        $this->write("\n");
    }

    public function endTest(Test $test, float $time): void
    {
        $info = \PHPUnit\Util\Test::describe($test);
        $isExtra = strpos($info[0], '\\Extra\\') !== false;
        $category = $this->getCategory($info[1]);

        if ($isExtra) {
            $this->hasExtraTests = true;
        }

        if (!isset($this->results[$category])) {
            $this->results[$category] = [];
        }

        if (!isset($this->results[$category][$info[1]])) {
            $this->results[$category][$info[1]] = [];
        }

        $this->results[$category][$info[1]][$isExtra ? 'extra' : 'main'] = !$this->lastTestFailed;

        parent::endTest($test, $time);
    }

    private function getCategory(string $name): string
    {
        if (substr($name, 0, 4) !== 'test') {
            return 'unknown';
        }

        foreach ($this->criteria as $category => $criterion) {
            if (substr($name, 4, strlen($category)) === $category) {
                return $category;
            }
        }

        return 'unknown';
    }
}
