--TEST--
Prevent rollback.
--EXPECT--
Should stage
Should fail run
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    RollbackEvent::class,
    fn (RollbackEvent $event) => $event->preventDefault()
);

$transaction = new Transaction(
    new Operation(
        'Illegal rollback',
        fn () => (print 'Should fail run' . PHP_EOL) < 0,
        fn () => throw new RuntimeException('Should not roll back'),
        fn () => (print 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();
