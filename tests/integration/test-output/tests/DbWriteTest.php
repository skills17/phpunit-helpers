<?php

namespace Skills17\PHPUnit\Test\Integration\TestOutput;

use Skills17\PHPUnit\Database\WriteTest;

class DbWriteTest extends WriteTest
{
    public function testDbWriteOutputEcho()
    {
        echo "This is an echo output\n";
        $this->assertTrue(true);
    }

    public function testDbWriteOutputVarDump()
    {
        var_dump(1234);
        $this->assertTrue(true);
    }

    public function testDbWriteOutputPrint()
    {
        print "This is a print output\n";
        $this->assertTrue(true);
    }

    public function testDbWriteOutputPrintR()
    {
        print_r(4321);
        $this->assertTrue(true);
    }
}
