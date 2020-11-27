<?php

namespace Skills17\PHPUnit;

class Config {
    /**
     * Get the test configuration.
     * Default values can be overwritten with environment variables.
     */
    public static function get()
    {
        $configFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.json';

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
