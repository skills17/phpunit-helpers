<?php

namespace Skills17\PHPUnit\Test\Integration\ConfigEnvOverwrite;

use Skills17\PHPUnit\BaseTest;
use Skills17\PHPUnit\Config;

class SomeTest extends BaseTest
{
    public function testDatabaseConfigOverwrite()
    {
        $this->assertEquals([
            'enabled' => true,
            'dump' => './db-dump-changed.sql',
            'name' => 'env-db',
            'user' => 'env-user',
            'password' => 'env-pw',
            'host' => 'env-host',
        ], Config::getInstance()->getDatabaseConfig());
    }
}
