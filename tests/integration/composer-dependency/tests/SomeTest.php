<?php

namespace Skills17\PHPUnit\Test\Integration\ComposerDependency;

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

    public function testDFoo()
    {
        $this->assertTrue(false);
    }

    public function testEFoo()
    {
        $this->assertTrue(false);
    }

    public function testEBar()
    {
        $this->assertTrue(false);
    }
}
