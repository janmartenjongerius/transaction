--TEST--
Rollbacks should be blocked on successful commits and repeated calls.
--EXPECT--
[BLOCK] > [rolled back: N] [committed: Y]
[BLOCK] > [rolled back: Y] [committed: Y]
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    RollbackBlockedEvent::class,
    fn(RollbackBlockedEvent $event) => print sprintf(
        '[BLOCK] > [rolled back: %s] [committed: %s]',
        $event->rolledBack ? 'Y' : 'N',
        $event->committed ? 'Y' : 'N'
    ) . PHP_EOL
);

$transaction = new Transaction();
$transaction->setDispatcher($dispatcher);
$result = $transaction->commit();

$result->rollback();
$result->rollback();
