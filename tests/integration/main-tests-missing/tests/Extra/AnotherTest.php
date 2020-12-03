<?php

namespace Skills17\PHPUnit\Test\Integration\ExtraTestsMissing\Extra;

use Skills17\PHPUnit\BaseTest;

/**
 * All tests in this class do not have a main test and should trigger a warning.
 */
class AnotherTest extends BaseTest
{
    public function testFFoo()
    {
        $this->assertTrue(true);
    }

    public function testFBar()
    {
        $this->assertTrue(false);
    }
}
