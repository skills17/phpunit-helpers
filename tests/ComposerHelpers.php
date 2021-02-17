<?php

namespace Skills17\PHPUnit\Test;

trait ComposerHelpers
{
    private function runComposerCommand(string $integrationTest, string $command): string
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'integration' . DIRECTORY_SEPARATOR . $integrationTest;
        return shell_exec('composer --working-dir=' . $dir . ' ' . $command . ' 2> /dev/null');
    }

    private function standardizeOutput(string $output): string
    {
        $result = preg_replace('/Time: \d{2}:\d{2}\.\d{3}, Memory: \d+\.\d+ [^\n]+\n/s', '', $output);
        $result = preg_replace('/\n[^\n]+(\/tests\/integration\/[a-zA-Z0-9_.\/-]+.php:\d+\n)/s', '$1', $result);
        $result = preg_replace('/PHPUnit \d+\.\d+\.\d+ by/s', 'PHPUnit * by', $result);

        return trim($result);
    }

    private function assertComposerCommandOutput(string $integrationTest, string $command, string $expectedFile)
    {
        $expectedOutputPath = __DIR__ . DIRECTORY_SEPARATOR . 'integration' . DIRECTORY_SEPARATOR . $integrationTest .
            DIRECTORY_SEPARATOR . $expectedFile;
        $this->assertTrue(file_exists($expectedOutputPath));
        $expectedOutput = file_get_contents($expectedOutputPath);

        $actualOutput = $this->runComposerCommand($integrationTest, $command);

        $this->assertEquals($this->standardizeOutput($expectedOutput), $this->standardizeOutput($actualOutput));
    }
}
