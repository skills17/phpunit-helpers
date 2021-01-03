<?php

namespace Skills17\PHPUnit\Test\Integration\RequiredTest;

use Skills17\PHPUnit\BaseTest;

class SomeTest extends BaseTest
{
    public function testAFoo()
    {
        $this->assertTrue(true);
    }

    public function testABar()
    {
        $this->assertTrue(false);
    }

    /**
     * Because this test fails and is required, group A should award 0 points
     */
    public function testARequired()
    {
        $this->assertTrue(false);
    }

    public function testBFoo()
    {
        $this->assertTrue(true);
    }

    public function testBBar()
    {
        $this->assertTrue(true);
    }

    /**
     * Because this test fails and is required, group B should award 0 points
     */
    public function testBRequired()
    {
        $this->assertTrue(false);
    }

    public function testCFoo()
    {
        $this->assertTrue(true);
    }

    public function testCRequired()
    {
        $this->assertTrue(true);
    }
}
