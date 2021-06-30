--TEST--
File can be copied to nested destination, creating directories as needed.
--EXPECTF--
name: johmanx10/transaction
file: /%s/copy-file.phpt.out/composer.json
--FILE--
<?php

declare(strict_types=1);

use Acme\Filesystem\Operation\CopyFile;
use Johmanx10\Transaction\Operation\OperationHandlerInterface;

/** @var OperationHandlerInterface $handler */
$handler = require __DIR__ . '/app/handler.php';
$destination = sys_get_temp_dir() . '/' . __FILE__ . '.out/composer.json';
$handler(
    CopyFile::fromPath(
        __DIR__ . '/../composer.json',
        $destination,
        overrideExisting: CopyFile::OVERRIDE_EXISTING_FILE
    )
);

$rootPackage = json_decode(file_get_contents($destination));
echo "name: {$rootPackage->name}\nfile: {$destination}";
