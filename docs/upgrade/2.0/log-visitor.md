# Log visitor

The previous version supplied a log [visitor](custom-visitor.md). The concept
of visitors has been replaced by [event dispatching](event-dispatcher.md).

There are two readily available
subscribers that log all events at appropriate log levels to any
[PSR-3 logger](https://www.php-fig.org/psr/psr-3/) compatible logger. This means
that the logger that is now used in the log visitor, can be reused in the loggin
subscriber(s).

To get closest to the previous behavior, use
`\Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber`.

However, in addition, to log information about the transaction and rollbacks,
consider using `\Johmanx10\Transaction\Event\TransactionLoggerSubscriber` next
to the previous subscriber.

This requires the package `symfony/event-dispatcher`, as the subscribers
implement `\Symfony\Component\EventDispatcher\EventSubscriberInterface`.

Both this library and `symfony/event-dispatcher` are compatible with
[PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/).
