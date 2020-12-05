<?php

namespace Skills17\PHPUnit\Test\Integration\Database;

use Skills17\PHPUnit\Database\ReadTest;

class SomeReadTest extends ReadTest
{
    /**
     * Username should be the one from the database dump as the db gets reset once before every read test.
     */
    public function testReadSomeFirst()
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

    /**
     * Username should already be changed since it is a read test and the database should not get reset between
     * two tests in the same class.
     */
    public function testReadSomeSecond()
    {
        $rows = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('changed', $rows[0]['username']);
    }
}
