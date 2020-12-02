<?php

namespace Skills17\PHPUnit\Test;

use DirectoryIterator;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    /**
     * @dataProvider integrationProvider
     */
    public function testIntegration(string $name): void
    {
        $this->assertCommandOutput($name, 'expected.txt');
    }

    public function integrationProvider(): array
    {
        $integrationTests = [];
        $dir = new DirectoryIterator(__DIR__);

        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && file_exists($fileInfo->getRealPath() . DIRECTORY_SEPARATOR . 'composer.json')) {
                $integrationTests[] = [$fileInfo->getBasename()];
            }
        }

        return $integrationTests;
    }

    private function runComposerCommand(string $integrationTest, string $command): string
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . $integrationTest;
        return shell_exec('composer --working-dir=' . $dir . ' ' . $command);
    }

    private function assertCommandOutput(string $integrationTest, string $expectedFile)
    {
        $expectedOutputPath = __DIR__ . DIRECTORY_SEPARATOR . $integrationTest . DIRECTORY_SEPARATOR . $expectedFile;
        $this->assertTrue(file_exists($expectedOutputPath));
        $expectedOutput = file_get_contents($expectedOutputPath);

        $actualOutput = $this->runComposerCommand($integrationTest, 'test');

        $this->assertEquals($this->standardizeOutput($expectedOutput), $this->standardizeOutput($actualOutput));
    }

    private function standardizeOutput(string $output): string
    {
        $result = preg_replace('/Time: \d{2}:\d{2}\.\d{3}, Memory: \d+\.\d+ [^\n]+\n/s', '', $output);
        $result = preg_replace('/\n[^\n]+(\/tests\/integration\/[a-zA-Z0-9_.\/-]+.php:\d+\n)/s', '$1', $result);
        return $result;
    }
}
