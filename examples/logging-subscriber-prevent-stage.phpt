--TEST--
Logging subscribers: prevent stages
--EXPECTF--
[debug] [stage] Staging: Successful operation
[debug] [stage] Prevented: Successful operation
[debug] [stage] Staging: Skipped operation
[debug] [stage] Prevented: Skipped operation
[debug] [stage] Staging: Broken operation
[debug] [stage] Prevented: Broken operation
[info] Transaction staged
[info] Transaction committed
[warning] Rollback was not allowed to proceed.
[debug] * Transaction successfully committed
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Johmanx10\Transaction\Operation\Event\StageEvent;
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
    StageEvent::class,
    fn (StageEvent $event) => $event->preventDefault()
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
