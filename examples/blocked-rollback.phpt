--TEST--
Rollbacks should be blocked on successful commits and repeated calls.
--EXPECT--
bool(true)
[BLOCK] > [rolled back: N] [committed: Y]
[BLOCK] > [rolled back: N] [committed: Y]
bool(false)
[BLOCK] > [rolled back: Y] [committed: N]
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\Operation\Operation;
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

var_dump($result->committed());
$result->rollback();
$result->rollback();

$transaction = new Transaction(
    new Operation('Broken operation', fn () => false)
);
$transaction->setDispatcher($dispatcher);
$result = $transaction->commit();

var_dump($result->committed());
$result->rollback();
$result->rollback();
