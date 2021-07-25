--TEST--
Logging subscribers: unstageable transaction
--EXPECTF--
[debug] [stage] Staging: Successful operation
[info] [stage] Staged: Successful operation
[debug] [stage] Staging: Skipped operation
[debug] [stage] Not required: Skipped operation
[debug] [stage] Staging: Broken operation
[warning] [stage] Not staged: Broken operation
[warning] Transaction could not be staged.
[info] [invoke] Successful operation
[debug] [invoke] Skipped: Successful operation
[info] [invoke] Broken operation
[debug] [invoke] Skipped: Broken operation
[error] Transaction not committed
[info] Performed 0 rollback(s)
[warning] Rollback was not allowed to proceed.
[debug] * Transaction previously rolled back
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Johmanx10\Transaction\Operation\OperationHandler;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\TransactionFactory;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$dispatcher = new EventDispatcher();
$factory = new TransactionFactory($dispatcher);
$handler = new OperationHandler($factory);
$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG);
$logger = new ConsoleLogger($output);

$dispatcher->addSubscriber(
    new TransactionLoggerSubscriber($logger)
);
$dispatcher->addSubscriber(
    new OperationLoggerSubscriber($logger)
);

$result = $handler(
    new Operation(
        description: 'Successful operation',
        invocation: fn () => throw new RuntimeException('Must not run')
    ),
    new Operation(
        description: 'Skipped operation',
        invocation: fn () => throw new RuntimeException('Must not run'),
        stage: fn () => StageResult::RESULT_SKIP
    ),
    new Operation(
        description: 'Broken operation',
        invocation: fn () => throw new RuntimeException('Break'),
        stage: fn () => StageResult::RESULT_FAILED
    )
);
$result->rollback();
