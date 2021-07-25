--TEST--
Custom rollback callable on operation handler.
--EXPECTF--
[error] Cannot override existing file: "/%s/custom-rollback.phpt.out/composer.json"
[error] [invoke] Failed: Copy file /%s/composer.json to /%s/custom-rollback.phpt.out
[error] Cannot override existing file: "/%s/custom-rollback.phpt.out/composer.json"
[error] Transaction not committed
[warning] [rollback] Rolling back: Transaction
--FILE--
<?php
declare(strict_types=1);

use Acme\Filesystem\Operation\CopyFile;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\OperationHandlerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var OperationHandlerInterface $handler */
$handler = require __DIR__ . '/app/handler.php';
$directory = sys_get_temp_dir() . '/' . __FILE__ . '.out';
$destination = $directory . '/composer.json';
$handler = $handler->withRollback(
    fn () => is_dir($directory) && `rm -rf "{$directory}"`
);
$result = $handler(
    CopyFile::fromPath(__DIR__ . '/../composer.json', $destination),
    new Operation(
        'Broken operation',
        fn () => false
    )
);

// The operation handler is configured to exit after a rollback.
// Use that to assert this statement is never reached.
echo 'Unreachable statement' . PHP_EOL;
