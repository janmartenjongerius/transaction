<?php

declare(strict_types=1);

use Johmanx10\Transaction\Examples\Filesystem\CopyFile;
use Johmanx10\Transaction\Operation\OperationHandlerInterface;

/** @var OperationHandlerInterface $handler */
$handler = require __DIR__ . '/app/handler.php';
$destination = __FILE__ . '.out/index.php';
$handler(
    CopyFile::fromPath(
        __FILE__,
        $destination,
        overrideExisting: CopyFile::OVERRIDE_EXISTING_FILE
    )
);
