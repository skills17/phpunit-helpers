<?php

namespace Skills17\PHPUnit\Test\Integration\HidePoints;

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

    public function testCBar()
    {
        $this->assertTrue(true);
    }

    /**
     * The extra test for this one fails and should trigger a warning.
     */
    public function testDFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * The extra test for this one fails and should trigger a warning.
     */
    public function testEFoo()
    {
        $this->assertTrue(true);
    }

    public function testEBar()
    {
        $this->assertTrue(true);
    }
}
