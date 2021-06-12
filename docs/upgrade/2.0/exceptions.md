# Exceptions

In the previous version, to take control of multiple cases, exception handling
and [custom visitors](custom-visitor.md) were the only ways in which this could
be achieved.

- `\Johmanx10\Transaction\Exception\TransactionRolledBackException`
- `\Johmanx10\Transaction\Exception\FailedRollbackException`
- `\Johmanx10\Transaction\Exception\OperationException`

By catching them and forwarding them to a corresponding formatter, the
developer could debug what caused the application to get in a specific state.

# Current exceptions

In the current version of the library are no exceptions, nor are they thrown
explicitly. Only when a rollback throws an exception, will it bubble up to the
surrounding application.

# Migrating

To understand the flow of transactions and operations, one can use
[event dispatching](event-dispatcher.md) to listen to specific
[events](../../events.md).

Alternatively, one can [register logging subscribers](log-visitor.md) to get a
sensible trace in a [PSR-3 logger](https://www.php-fig.org/psr/psr-3/)
compatible logger.
