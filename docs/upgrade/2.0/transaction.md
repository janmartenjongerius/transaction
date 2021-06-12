# Transaction

In the previous major version, when a transaction failed, it was immediately
rolled back. This meant that the result of a transaction could be:

- It succeeded
- It automatically rolled back
- The automatic rollback failed

The developer / user had no say in this.

## Changes

- No more automatic [rollbacks](../../transaction.md#rollbacks).
- [Staging of operations](../../operation.md#staging).
- Ability to [dry-run](../../transaction.md#dry-runs)
- The transaction is now stateless and only describes a list of operations.

## Upgrading

The following describes steps needed to upgrade:

### Check if the transaction has committed

The method `isCommitted` has been replaced by `committed` on the commit result
object.

**Old case**

```php
try {
    $transaction->commit();
} catch (TransactionRolledBackException | FailedRollbackException $exception) {
    // Do something with the exception.
}
$transaction->isCommitted();
```

**New case**

```php
$result = $transaction->commit();
$result->committed();
```

Or, with a [listener](../../events.md) for
`\Johmanx10\Transaction\Event\CommitResultEvent`.

### Rollback

Rolling back used to happen automatically.

**Old case**

```php
try {
    $transaction->commit();
} catch (FailedRollbackException $exception) {
    // Take care of failed rollback.
}
```

**New case**

```php
$result = $transaction->commit();

if (!$result->committed()) {
    try {
        $result->rollback();        
    } catch (\Throwable $exception) {
        // Take care of the failed rollback.
    }
}
```

### Operation failures

A list of failed operations could be read from the `TransactionRolledBackException`.

This is no longer possible. Instead, if the failed operations are valuable, they
can be gatheres through an [event listener](../../events.md) on
`\Johmanx10\Transaction\Operation\Event\InvocationResultEvent`.


**Old case**

```php
try {
    $transaction->commit();
} catch (TransactionRolledBackException $exception) {
    $failures = $exception->getFailures();
}
```

**New case**

The class `\Acme\Transaction\Operation\InvocationWatcher` is a made up class
that merely demonstrates how to go about getting the legacy behavior back.

```php
$invocationWatcher = new \Acme\Transaction\Operation\InvocationWatcher();
$dispatcher->addListener(InvocationResultEvent::class, $invocationWatcher);
$transaction->setDispatcher($dispatcher);

$transaction->commit();

$failures = $invocationWatcher->getFailures();
```
