<?php

namespace Skills17\PHPUnit;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{

    public $db = null;

    /**
     * Initialize database connection and setup http client
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $config = Config::get();

        if (!isset($config['database']) || $config['database']) {
            $dsn = 'mysql:host=127.0.0.1;dbname=bikesharing;charset=utf8mb4';

            try {
                $this->db = new PDO($dsn, 'root', '', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'']);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo 'Unable to connect to the database! ' . $e->getMessage() . "\n";
                echo 'DSN: ' . $dsn . ', User: root, Password: no' . "\n";
                exit(1);
            }
        }
    }

    /**
     * Reset the database with the dump located in data/db-dump.sql
     */
    protected function resetDb()
    {
        if ($this->db === null) {
            return;
        }

        $dumpFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'db-dump.sql';

        if (!file_exists($dumpFile)) {
            echo "Database dump (db-dump.sql) does not exist\n";
            exit(1);
        }

        $lines = file($dumpFile);
        $statement = '';

        $this->db->beginTransaction();

        // parse the dump because PDO can only execute one statement at a time
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if ($trimmedLine === '' || substr($trimmedLine, 0, 2) === '--') {
                continue;
            }

            $statement .= $trimmedLine;
            if (substr($trimmedLine, -1) === ';') {
                try {
                    $this->db->exec($statement);
                } catch (\PDOException $e) {
                    $this->db->rollBack();
                    echo 'Error during DB reset: ' . $e->getMessage() . "\n";
                    echo 'Statement: ' . $statement . "\n";
                    exit(1);
                }
                $statement = '';
            }
        }

        $this->db->commit();
    }

    /**
     * Resets the timezone in both PHP and MySQL to avoid issues when working with dates.
     */
    private function resetTimezone()
    {
        date_default_timezone_set('Europe/Zurich');
        $this->db->exec('SET time_zone = "+02:00";');
    }

    /**
     * For the tests, reset the database before every test
     */
    public function setUp(): void
    {
        $this->resetDb();
        $this->resetTimezone();
    }
}
