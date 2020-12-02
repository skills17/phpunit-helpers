<?php

$dir = new DirectoryIterator(__DIR__);

foreach ($dir as $fileInfo) {
    if ($fileInfo->isDir() && file_exists($fileInfo->getRealPath() . DIRECTORY_SEPARATOR . 'composer.json')) {
        echo "\nInstalling tests/integration/" . $fileInfo->getBasename() . "...\n";
        system('composer --working-dir=' . $fileInfo->getRealPath() . ' install --ansi');
    }
}
