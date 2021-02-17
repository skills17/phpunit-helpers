<?php

namespace Skills17\PHPUnit\Database;

abstract class ReadTest extends DatabaseTest
{
    /**
     * For read tests, only reset the database once before the testsuite runs
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!isset(self::$dbReset[get_class($this)])) {
            $this->resetDb();

            self::$dbReset[get_class($this)] = true;
        }
    }
}
