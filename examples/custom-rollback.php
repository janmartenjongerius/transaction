<?php

declare(strict_types=1);

use Johmanx10\Transaction\Examples\Filesystem\CopyFile;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\OperationHandlerInterface;

// An error will always occur. This is not a bad state, so exit with 0.
const EXIT_ON_ERROR = 0;

/** @var OperationHandlerInterface $handler */
$handler = require __DIR__ . '/app/handler.php';
$directory = __FILE__ . '.out';
$destination = $directory . '/index.php';
$handler = $handler->withRollback(
    fn () => is_dir($directory) && `rm -rf "{$directory}"`
);
$result = $handler(
    CopyFile::fromPath(__FILE__, $destination),
    new Operation(
        'Broken operation',
        fn () => false
    )
);
