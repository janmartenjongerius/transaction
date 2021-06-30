--TEST--
Prevent invocation.
--EXPECT--
Should stage
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    InvocationEvent::class,
    fn (InvocationEvent $event) => $event->preventDefault()
);

$transaction = new Transaction(
    new Operation(
        'Illegal invocation',
        fn () => throw new RuntimeException( 'Should not run'),
        fn () => throw new RuntimeException( 'Should not roll back'),
        fn () => (print 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();
