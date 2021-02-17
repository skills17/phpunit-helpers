<?php

namespace Skills17\PHPUnit\Database;

abstract class WriteTest extends DatabaseTest
{
    /**
     * For write tests, reset the database before every test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->resetDb();
    }
}
