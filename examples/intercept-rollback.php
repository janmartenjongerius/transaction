<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Rollback;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

$outFile = __FILE__ . '.out';
$log = fopen($outFile, 'wb+');

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    RollbackEvent::class,
    fn (RollbackEvent $event) => $event->rollback = new Rollback(
        'Intercepted invocation',
        fn () => fwrite($log, 'Intercept successful' . PHP_EOL)
    )
);

$transaction = new Transaction(
    new Operation(
        'Intercept rollback',
        fn () => fwrite($log,  'Should fail invocation' . PHP_EOL) < 0,
        fn () => throw new RuntimeException( 'Should be intercepted' . PHP_EOL),
        fn () => fwrite($log, 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction
    ->commit()
    ->rollback();

readfile($outFile);
