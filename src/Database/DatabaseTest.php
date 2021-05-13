<?php

namespace Skills17\PHPUnit\Database;

use PDO;
use Skills17\PHPUnit\BaseTest;
use Skills17\PHPUnit\Config;

abstract class DatabaseTest extends BaseTest
{
    public $db = null;
    protected static $dbReset = [];

    /**
     * Initialize database connection and setup http client
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);

        if (!Config::getInstance()->hasDatabase()) {
            $this->writeLine('No database configured.');
            $this->writeLine(
                'Either enable a database in the config.json file',
                'or extend the BaseTest class instead of ReadTest or WriteTest.'
            );
            exit(1);
        }

        $dbConfig = Config::getInstance()->getDatabaseConfig();
        $dsn = 'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['name'] . ';charset=utf8mb4';

        try {
            $this->db = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            ]);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->writeLine('Unable to connect to the database!', $e->getMessage());
            $this->writeLine('DSN: ' . $dsn . ', User: ' . $dbConfig['user'] . ', Password: ' .
                ($dbConfig['password'] ? str_pad('', strlen($dbConfig['password']), '*') : '(no password)'));
            exit(1);
        }
    }

    /**
     * Reset the database with provided database dump
     */
    protected function resetDb()
    {
        $dumpFile = Config::getProjectRoot() . Config::getInstance()->getDatabaseConfig()['dump'];

        if (!file_exists($dumpFile)) {
            $this->writeLine('Database dump (' . $dumpFile . ') does not exist');
            exit(1);
        }

        $lines = file($dumpFile);
        $statement = '';

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
                    $this->writeLine('Error during DB reset:', $e->getMessage());
                    $this->writeLine('Statement:', $statement);
                    exit(1);
                }
                $statement = '';
            }
        }
    }
}
