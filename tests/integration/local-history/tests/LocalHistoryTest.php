<?php

namespace Skills17\PHPUnit\Test\Integration\LocalHistory;

use Skills17\PHPUnit\BaseTest;

class LocalHistoryTest extends BaseTest
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
        $this->assertTrue(false);
    }

    public function testCFoo()
    {
        $this->assertTrue(false);
    }
}
