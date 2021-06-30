--TEST--
Stages can be intercepted.
--EXPECT--
Intercept successful
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\StageEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Stage;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    StageEvent::class,
    fn (StageEvent $event) => $event->stage = new Stage(
        $event->stage->operation,
        fn () => (print 'Intercept successful' . PHP_EOL) < 0
    )
);

$transaction = new Transaction(
    new Operation(
        'Intercept stage',
        fn () => throw new RuntimeException( 'Should not run'),
        fn () => throw new RuntimeException( 'Should not roll back'),
        fn () => throw new RuntimeException( 'Should be intercepted')
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();
