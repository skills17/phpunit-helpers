<?php

namespace Skills17\PHPUnit\Test\Integration;

use DirectoryIterator;
use PHPUnit\Framework\TestCase;
use Skills17\PHPUnit\Test\ComposerHelpers;

class IntegrationTest extends TestCase
{
    use ComposerHelpers;

    /**
     * @dataProvider integrationProvider
     */
    public function testIntegration(string $name): void
    {
        $this->assertComposerCommandOutput($name, 'test', 'expected.txt');
        $this->assertComposerCommandOutput($name, 'test:json', 'expected.json');
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
}
