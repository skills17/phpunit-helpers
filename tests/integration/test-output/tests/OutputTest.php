<?php

namespace Skills17\PHPUnit\Test\Integration\TestOutput;

use Skills17\PHPUnit\BaseTest;

class OutputTest extends BaseTest
{
    public function testOutputEcho()
    {
        echo "This is an echo output\n";
        $this->assertTrue(true);
    }

    public function testOutputVarDump()
    {
        var_dump(1234);
        $this->assertTrue(true);
    }

    public function testOutputPrint()
    {
        print "This is a print output\n";
        $this->assertTrue(true);
    }

    public function testOutputPrintR()
    {
        print_r(4321);
        $this->assertTrue(true);
    }
}
