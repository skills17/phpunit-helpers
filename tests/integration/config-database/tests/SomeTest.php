<?php

namespace Skills17\PHPUnit\Test\Integration\ConfigDatabase;

use Skills17\PHPUnit\BaseTest;
use Skills17\PHPUnit\Config;

class SomeTest extends BaseTest
{
    public function testDatabaseConfigOverwrite()
    {
        $this->assertEquals([
            'enabled' => true,
            'dump' => './db-dump-changed.sql',
            'name' => 'skills17-changed',
            'user' => 'anotherone',
            'password' => 'supersecret',
            'host' => 'db-host',
        ], Config::getInstance()->getDatabaseConfig());
    }
}
