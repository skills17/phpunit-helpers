<?php

namespace Skills17\PHPUnit\Test\Integration\DefaultStrategy;

use Skills17\PHPUnit\BaseTest;

class SomeTest extends BaseTest
{
    /**
     * Group A
     * Should award 1/2 points as strategy is deduct and max points 2
     */
    public function testAFoo()
    {
        $this->assertTrue(true);
    }

    public function testABar()
    {
        $this->assertTrue(true);
    }

    public function testABaz()
    {
        $this->assertTrue(false);
    }

    /**
     * Group B
     * Should award 2/3 points as strategy is add
     */
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
}
