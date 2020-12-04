<?php

namespace Skills17\PHPUnit\Test\Integration\ExtraTestsStrategyDeduct;

use Skills17\PHPUnit\BaseTest;

class SomeTest extends BaseTest
{
    /**
     * Group A
     * Should result in 2/3 as ABar deducts 1 point
     */
    public function testAFoo()
    {
        $this->assertTrue(true);
    }

    public function testABar()
    {
        $this->assertTrue(false);
    }

    public function testABaz()
    {
        $this->assertTrue(true);
    }

    /**
     * Group B
     * Should result in 1/2 as BBar deducts 1 point and max is set to 2
     */
    public function testBFoo()
    {
        $this->assertTrue(true);
    }

    public function testBBar()
    {
        $this->assertTrue(false);
    }

    public function testBBaz()
    {
        $this->assertTrue(true);
    }

    /**
     * Group C
     * Should result in 0/2 as a value below 0 is not possible
     */
    public function testCFoo()
    {
        $this->assertTrue(false);
    }

    public function testCBar()
    {
        $this->assertTrue(false);
    }

    public function testCBaz()
    {
        $this->assertTrue(false);
    }

    /**
     * Group D
     * Should result in 1/1.5 as default points is 0.5
     */
    public function testDFoo()
    {
        $this->assertTrue(true);
    }

    public function testDBar()
    {
        $this->assertTrue(false);
    }

    public function testDBaz()
    {
        $this->assertTrue(true);
    }

    /**
     * Group E
     * Should result in 0.5/2 as one test deducts more points
     */
    public function testEFoo()
    {
        $this->assertTrue(true);
    }

    public function testEMorePoints()
    {
        $this->assertTrue(false);
    }

    public function testEBar()
    {
        $this->assertTrue(false);
    }

    public function testEBaz()
    {
        $this->assertTrue(true);
    }

    /**
     * Group F
     * Should result in 2/2
     */
    public function testFFoo()
    {
        $this->assertTrue(true);
    }

    public function testFBar()
    {
        $this->assertTrue(true);
    }
}
