<?php

namespace Skills17\PHPUnit;

use Error;
use ReflectionClass;

class Config
{
    private static $instance;
    private $config;
    private $defaultConfig = [
        'database' => [
            'enabled' => false,
            'dump' => './database.sql',
            'name' => 'skills17',
            'user' => 'root',
            'password' => '',
            'host' => '127.0.0.1',
        ],
        'points' => [
            'defaultPoints' => 1.0,
            'strategy' => 'add',
        ],
        'groups' => [],
    ];

    /**
     * Create a new config instance and merge it with the default config values.
     */
    private function __construct($config)
    {
        $this->config = $this->mergeConfig($this->defaultConfig, $config);
        $this->validate();
    }

    /**
     * Get the project root folder
     */
    public static function getProjectRoot(): string
    {
        // get project root
        $classLoader = new ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $projectRoot = dirname($classLoader->getFileName(), 3);

        // if the config.json does not exist in the assumed project directory, check if it exists in the current working
        // directory and use it instead. this is mainly used during tests.
        if (!file_exists($projectRoot . DIRECTORY_SEPARATOR . 'config.json')) {
            if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'config.json')) {
                return getcwd() . '/';
            }
        }

        return $projectRoot . '/';
    }

    /**
     * Get the test configuration instance.
     */
    public static function getInstance(): Config
    {
        if (self::$instance) {
            return self::$instance;
        }

        $configFile = self::getProjectRoot() . 'config.json';

        if (!file_exists($configFile)) {
            throw new Error('Config file (' . $configFile . ') does not exist');
        }

        $jsonConfig = json_decode(file_get_contents($configFile), true);

        if ($jsonConfig === null) {
            throw new Error('Could not decode config file (config.json)');
        }

        self::$instance = new self($jsonConfig);

        return self::$instance;
    }

    /**
     * Checks if a database should be used for the tests.
     */
    public function hasDatabase(): bool
    {
        return $this->config['database']['enabled'];
    }

    /**
     * Gets the database config.
     */
    public function getDatabaseConfig(): array
    {
        $envConfig = array_filter([
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
        ]);

        return array_merge($this->config['database'], $envConfig);
    }

    /**
     * Get the default points.
     */
    public function getDefaultPoints(): float
    {
        return $this->config['points']['defaultPoints'];
    }

    /**
     * Get the points strategy.
     * Can be either 'add' or 'deduct'.
     */
    public function getPointsStrategy(): string
    {
        return $this->config['points']['strategy'];
    }

    /**
     * Gets all test groups
     */
    public function getGroups(): array
    {
        return $this->config['groups'] ?? [];
    }

    /**
     * Get the output format.
     * Can be either 'json' or 'text'.
     */
    public function getFormat(): string
    {
        return getenv('FORMAT') ?: 'text';
    }

    /**
     * Merge two configuration arrays.
     */
    private function mergeConfig(array $config1, array $config2): array
    {
        $merged = $config1;

        foreach ($config2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeConfig($merged[$key], $value);
            } elseif (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Validates the current configuration.
     */
    private function validate()
    {
        $format = $this->getFormat();
        $strategy = $this->getPointsStrategy();

        // validate if a valid format is specified
        if ($format !== 'text' && $format !== 'json') {
            throw new Error('config.json validation error: Invalid output format: ' . $format);
        }

        // validate points strategy
        if ($strategy !== 'add' && $strategy !== 'deduct') {
            throw new Error('config.json validation error: Invalid points strategy: ' . $format);
        }

        // validate test groups
        foreach ($this->getGroups() as $groupIndex => $group) {
            if (!isset($group['match'])) {
                throw new Error('config.json validation error: Group #' . $groupIndex .
                    ' does not contain a "match" property');
            }

            if (isset($group['strategy']) && $group['strategy'] !== 'add' && $group['strategy'] !== 'deduct') {
                throw new Error('config.json validation error: Invalid points strategy: ' . $format);
            }

            foreach (($group['tests'] ?? []) as $testIndex => $test) {
                if (!isset($test['match'])) {
                    throw new Error('config.json validation error: Test #' . $testIndex .
                        ' in group #' . $groupIndex . ' (' . $group['match'] . ') does not contain a "match" property');
                }
            }

            if (
                isset($group['maxPoints']) && (
                    (isset($group['strategy']) && $group['strategy'] !== 'deduct') ||
                    (!isset($group['strategy']) && $this->getPointsStrategy() !== 'deduct')
                )
            ) {
                throw new Error('config.json validation error: Property "maxPoints" can only be set for strategy ' .
                    '"deduct". Found in group #' . $groupIndex . ' (' . $group['match'] . ')');
            }
        }
    }
}
