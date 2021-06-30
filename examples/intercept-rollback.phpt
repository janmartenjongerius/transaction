--TEST--
Rollbacks can be intercepted.
--EXPECT--
Should stage
Should fail invocation
Intercept successful
--FILE--
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Rollback;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    RollbackEvent::class,
    fn (RollbackEvent $event) => $event->rollback = new Rollback(
        'Intercepted rollback',
        fn () => (print 'Intercept successful' . PHP_EOL) > 0
    )
);

$transaction = new Transaction(
    new Operation(
        'Intercept rollback',
        fn () => (print  'Should fail invocation' . PHP_EOL) < 0,
        fn () => throw new RuntimeException( 'Should be intercepted'),
        fn () => (print 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction
    ->commit()
    ->rollback();
