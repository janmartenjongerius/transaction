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

$outFile = __FILE__ . '.out';
$log = fopen($outFile, 'wb+');

$transaction = new Transaction(
    new Operation(
        'Illegal invocation',
        fn () => throw new RuntimeException( 'Should not run' . PHP_EOL),
        fn () => throw new RuntimeException( 'Should not roll back' . PHP_EOL),
        fn () => fwrite($log, 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();

readfile($outFile);
