<?php

namespace Skills17\PHPUnit\Test\Integration\Database;

use Skills17\PHPUnit\Database\ReadTest;

class AnotherReadTest extends ReadTest
{
    /**
     * Username should be the one from the database dump as the db gets reset once before every read test.
     */
    public function testReadAnotherFirst()
    {
        $rows = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('admin', $rows[0]['username']);

        // change username
        $this->db->query('UPDATE users SET username = \'changed\' WHERE id = 1');

        $changedRows = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchAll();

        $this->assertCount(1, $changedRows);
        $this->assertEquals('changed', $changedRows[0]['username']);
    }

    public function testReadAnotherSecond()
    {
        $rows = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('changed', $rows[0]['username']);
    }
}
