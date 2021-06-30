--TEST--
Prevent stage.
--EXPECT--
bool(true)
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\StageEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    StageEvent::class,
    fn (StageEvent $event) => $event->preventDefault()
);

$transaction = new Transaction(
    new Operation(
        'Illegal stage',
        fn () => throw new RuntimeException('Should not run'),
        fn () => throw new RuntimeException('Should not roll back'),
        fn () => throw new RuntimeException('Should not stage')
    )
);
$transaction->setDispatcher($dispatcher);
$result = $transaction->commit();

var_dump($result->committed());
