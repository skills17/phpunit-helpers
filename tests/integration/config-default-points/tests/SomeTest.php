<?php

namespace Skills17\PHPUnit\Test\Integration\ConfigDefaultPoints;

use Skills17\PHPUnit\BaseTest;

class SomeTest extends BaseTest
{
    /**
     * Should award 3 points
     */
    public function testAFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 3 points
     */
    public function testABar()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 0 points
     */
    public function testABaz()
    {
        $this->assertTrue(false);
    }

    /**
     * Should award 0.5 points
     */
    public function testBFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 0.5 points
     */
    public function testBBar()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 0 points
     */
    public function testBBaz()
    {
        $this->assertTrue(false);
    }

    /**
     * Should award 3 points
     */
    public function testCFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 2 points
     */
    public function testCLessPoints()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 0 points
     */
    public function testCBaz()
    {
        $this->assertTrue(false);
    }

    /**
     * Should award 1 point
     */
    public function testDFoo()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 2 points
     */
    public function testDMorePoints()
    {
        $this->assertTrue(true);
    }

    /**
     * Should award 0 points
     */
    public function testDBaz()
    {
        $this->assertTrue(false);
    }
}
