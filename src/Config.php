<?php

namespace Skills17\PHPUnit;

use ReflectionClass;

class Config
{
    /**
     * Get the test configuration.
     * Default values can be overwritten with environment variables.
     */
    public static function get()
    {
        // get project root
        $classLoader = new ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $projectRoot = dirname($classLoader->getFileName(), 3);

        $configFile = $projectRoot() . 'config.json';

        if (!file_exists($configFile)) {
            echo "Config file (config.json) does not exist\n";
            exit(1);
        }

        $jsonConfig = json_decode(file_get_contents($configFile), true);

        if ($jsonConfig === null) {
            echo "Could not decode config file (config.json)\n";
            exit(1);
        }

        return array_merge($jsonConfig, [
            'format' => getenv('FORMAT') ?? 'normal',
        ]);
    }
}
