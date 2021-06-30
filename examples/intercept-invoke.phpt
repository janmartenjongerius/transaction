--TEST--
Invocations can be intercepted.
--EXPECT--
Should stage
Intercept successful
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    InvocationEvent::class,
    fn (InvocationEvent $event) => $event->invocation = new Invocation(
        'Intercepted invocation',
        fn () => (print 'Intercept successful' . PHP_EOL) > 0,
        fn () => null
    )
);

$transaction = new Transaction(
    new Operation(
        'Intercept invocation',
        fn () => throw new RuntimeException( 'Should be intercepted'),
        fn () => throw new RuntimeException( 'Should not roll back'),
        fn () => (print 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();
