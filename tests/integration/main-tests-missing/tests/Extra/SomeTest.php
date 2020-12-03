<?php

namespace Skills17\PHPUnit\Test\Integration\ExtraTestsMissing\Extra;

use Skills17\PHPUnit\BaseTest;

class SomeTest extends BaseTest
{
    public function testAFoo()
    {
        $this->assertTrue(true);
    }

    public function testBFoo()
    {
        $this->assertTrue(true);
    }

    public function testBBar()
    {
        $this->assertTrue(true);
    }

    public function testBBaz()
    {
        $this->assertTrue(false);
    }

    public function testCFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * This test does not have a main test and should trigger a warning.
     */
    public function testCBar()
    {
        $this->assertTrue(true);
    }

    public function testDFoo()
    {
        $this->assertTrue(true);
    }

    public function testEFoo()
    {
        $this->assertTrue(true);
    }

    public function testEBar()
    {
        $this->assertTrue(true);
    }
}
