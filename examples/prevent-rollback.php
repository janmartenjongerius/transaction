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

$outFile = __FILE__ . '.out';
$log = fopen($outFile, 'wb+');

$transaction = new Transaction(
    new Operation(
        'Prevented rollback',
        fn () => fwrite($log, 'Should run' . PHP_EOL) > 0,
        fn () => throw new RuntimeException('Should not roll back' . PHP_EOL),
        fn () => fwrite($log, 'Should stage' . PHP_EOL) > 0
    )
);
$transaction->setDispatcher($dispatcher);
$transaction->commit();

readfile($outFile);
