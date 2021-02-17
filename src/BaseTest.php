<?php

namespace Skills17\PHPUnit;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        // suppress output for the json format
        if (Config::getInstance()->getFormat() === 'json') {
            $this->setOutputCallback(function () {
            });
        }
    }

    public function writeLine(...$args)
    {
        echo implode(' ', $args) . "\n";
    }
}
