<?php

namespace Skills17\PHPUnit\Test\Integration\TestOutput;

use Skills17\PHPUnit\Database\ReadTest;

class DbReadTest extends ReadTest
{
    public function testDbReadOutputEcho()
    {
        echo "This is an echo output\n";
        $this->assertTrue(true);
    }

    public function testDbReadOutputVarDump()
    {
        var_dump(1234);
        $this->assertTrue(true);
    }

    public function testDbReadOutputPrint()
    {
        print "This is a print output\n";
        $this->assertTrue(true);
    }

    public function testDbReadOutputPrintR()
    {
        print_r(4321);
        $this->assertTrue(true);
    }
}
