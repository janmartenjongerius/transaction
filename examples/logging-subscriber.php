<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Johmanx10\Transaction\Examples\Filesystem\CopyFile;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Johmanx10\Transaction\Operation\OperationHandler;
use Johmanx10\Transaction\TransactionFactory;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

/** @var ConsoleOutput $output */
[, $output] = require __DIR__ . '/app/console.php';
$dispatcher = new EventDispatcher();
$factory = new TransactionFactory($dispatcher);
$handler = new OperationHandler($factory);
$logger = new ConsoleLogger($output);

$dispatcher->addSubscriber(
    new TransactionLoggerSubscriber($logger)
);
$dispatcher->addSubscriber(
    new OperationLoggerSubscriber($logger)
);

$destination = __FILE__ . '.out/index.php';
$result = $handler(
    CopyFile::fromPath(
        source: __FILE__,
        destination: $destination,
        overrideExisting: CopyFile::OVERRIDE_EXISTING_FILE
    ),
    CopyFile::fromPath(
        source: __DIR__ . '/../vendor/autoload.php',
        destination: $destination,
        overrideExisting: CopyFile::PRESERVE_EXISTING_FILE
    )
);
