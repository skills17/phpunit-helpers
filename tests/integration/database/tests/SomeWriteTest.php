<?php

namespace Skills17\PHPUnit\Test\Integration\Database;

use Skills17\PHPUnit\Database\WriteTest;

class SomeWriteTest extends WriteTest
{
    /**
     * Username should be the one from the database dump as the db gets reset once before every write test.
     */
    public function testWriteFirst()
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
     * Additionally, the db gets reset before every test inside a write test class so it should also equal
     * the one from the dump again here.
     */
    public function testWriteSecond()
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
}
