<?php

namespace Skills17\PHPUnit\Test\Integration\ExtraTestsMissing\Extra;

use Skills17\PHPUnit\BaseTest;

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
