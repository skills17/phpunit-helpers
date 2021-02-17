<?php

namespace Skills17\PHPUnit\Test\Integration;

use PHPUnit\Framework\TestCase;
use Skills17\PHPUnit\Test\ComposerHelpers;

class LocalHistoryTest extends TestCase
{
    use ComposerHelpers;

    private $historyDir = __DIR__ . DIRECTORY_SEPARATOR . 'local-history' . DIRECTORY_SEPARATOR . '.history';

    public function setUp(): void
    {
        if (file_exists($this->historyDir)) {
            rmrdir($this->historyDir);
        }
    }

    public function testLocalHistory(): void
    {
        $this->assertFalse(file_exists($this->historyDir));
        $this->runComposerCommand('local-history', 'test');
        $this->runComposerCommand('local-history', 'test:json');
        $this->assertTrue(file_exists($this->historyDir));

        $history = array_filter(scandir($this->historyDir), function ($name) {
            return $name !== '.' && $name !== '..';
        });

        $this->assertEquals(2, count($history));

        foreach ($history as $file) {
            $json = json_decode(file_get_contents($this->historyDir . DIRECTORY_SEPARATOR . $file));
            $this->assertIsNumeric($json->time);
            $this->assertIsArray($json->testResults);
        }
    }

    public function testLocalHistoryDisabledByDefault(): void
    {
        $historyPath = __DIR__ . DIRECTORY_SEPARATOR . 'config-minimal' . DIRECTORY_SEPARATOR . '.history';

        $this->runComposerCommand('config-minimal', 'test');
        $this->assertFalse(file_exists($historyPath));
    }
}
