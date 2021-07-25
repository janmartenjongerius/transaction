--TEST--
Logging subscribers: prevent invocations
--EXPECTF--
[debug] [stage] Staging: Successful operation
[info] [stage] Staged: Successful operation
[debug] [stage] Staging: Skipped operation
[debug] [stage] Not required: Skipped operation
[debug] [stage] Staging: Successful operation
[info] [stage] Staged: Successful operation
[info] Transaction staged
[info] [invoke] Successful operation
[debug] [invoke] Prevented: Successful operation
[info] [invoke] Successful operation
[debug] [invoke] Prevented: Successful operation
[info] Transaction committed
[warning] Rollback was not allowed to proceed.
[debug] * Transaction successfully committed
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;
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
$dispatcher->addListener(
    InvocationEvent::class,
    fn (InvocationEvent $event) => $event->preventDefault()
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
        description: 'Successful operation',
        invocation: fn () => throw new RuntimeException('Must not run')
    )
);
$result->rollback();
