# Event dispatcher

The concept of [visitors](custom-visitor.md) has been replaced by event
dispatching. It is compatible with
[PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/).

This allows the implementor more agency and granular control by implementing
listeners for [events](../../events.md) and by using their control mechanisms.

> If one uses `symfony/event-dispatcher`, there are two readily available
subscribers that log all events at appropriate log levels to any
[PSR-3 logger](https://www.php-fig.org/psr/psr-3/) compatible logger.
>
> - `\Johmanx10\Transaction\Event\TransactionLoggerSubscriber`
> - `\Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber`
