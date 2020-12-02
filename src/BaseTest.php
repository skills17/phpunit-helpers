<?php

namespace Skills17\PHPUnit;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function writeLine(...$args)
    {
        echo implode(' ', $args) . "\n";
    }
}
