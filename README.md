[![Build Status](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/build.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/build-status/master)
[![Packagist](https://img.shields.io/packagist/dt/johmanx10/transaction.png)](https://packagist.org/packages/johmanx10/transaction/stats)
[![Packagist](https://img.shields.io/packagist/v/johmanx10/transaction.png)](https://packagist.org/packages/johmanx10/transaction)
![PHP from Packagist](https://img.shields.io/packagist/php-v/johmanx10/transaction.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/?branch=master)
![Packagist](https://img.shields.io/packagist/l/johmanx10/transaction.svg)


# Introduction

Transaction handles operations with automatic rollback mechanisms.

A transaction consists of operations. When an operation fails, it traverses back
up the chain, rolling back all previous operations in reverse order.

Assume a situation where filesystem operations need to be automated. If a part
of the operations fail, the filesystem needs to be restored to the state before
all operations were applied. Given the following operations:

1. Create directory `my-app`
2. Copy file `dist/console` to `my-app/bin/console`
3. Add executable rights to `my-app/bin/console`

This will be handled as follows:

1. ✔ Create directory `my-app`.
2. ∴ Copy file `dist/console` to `my-app/bin/console` - Directory `my-app/bin`
   does not exist.
3. ✔ Rollback: if `my-app/bin/console` exists, remove it.
4. ✔ Rollback: if `my-app` exists, remove it.

An [example of the above](examples/file-operations) can be tested locally by
running `examples/file-operations` from a command line terminal.

Every operation is responsible for defining their own rollback mechanism. That
way, complex nested structures to check and roll back operations can be
constructed vertically.

# Installation

```
composer require johmanx10/transaction
```

# Processing operations

To process a list of ordered operations, use a transaction:

```php
<?php
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Exception\TransactionRolledBackException;
use Johmanx10\Transaction\Exception\FailedRollbackException;

/** @var OperationInterface[] $operations */
$transaction = new Transaction(...$operations);

try {
    $transaction->commit();
} catch (TransactionRolledBackException $rollback) {
    // Do something with the operations that were rolled back.
    // This exception contains a method to get all failed operations, paired
    // with any exception that triggered the rollback.
} catch (FailedRollbackException $rollbackException) {
    // Do something if an operation could not be rolled back.
    // This exception contains the affected operation, as well as a list of
    // operations that have successfully rolled back up to the point where the
    // current operation could not.
}
```

# Defining an operation

To create an operation, implement the `OperationInterface`,
`DescribableOperationInterface` or use the existing `Operation` class to create
and inline operation:

```php
<?php
use Johmanx10\Transaction\Operation;
use Johmanx10\Transaction\Transaction;

$appDir = __DIR__ . '/my-app';

$transaction = new Transaction(
    // Create the app directory.
    new Operation(
        // Create the new directory.
        function () use ($appDir) {
            if (!file_exists($appDir) && !@mkdir($appDir)) {
                throw new RuntimeException(
                    sprintf('Could not create directory: "%s"', $appDir)
                );
            }
        },
        // Roll back the operation.
        function () use ($appDir) {
            if (file_exists($appDir) && !@rmdir($appDir)) {
                throw new RuntimeException(
                    sprintf('Could not remove directory: "%s"', $appDir)
                );
            }
        },
        // Set the operation description.
        sprintf('Create directory: "%s"', $appDir)
    )
);
```

# Formatting operations and exceptions

To better identify the operation, the operation failure or a specific exception,
a number of formatters are available to help with debugging failed operations,
chains of rolled back operations or failing rollbacks.

## Operation formatter

The operation formatter can be used to format an operation.
If an operation implements the `DescribableOperationInterface`, it can be
converted to string and will be represented as such. Otherwise, it will create
a generic representation, with a unique identifier for the operation.

```php
<?php
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Formatter\OperationFormatterInterface;

/**
 * @var OperationFormatterInterface $formatter
 * @var OperationInterface          $operation 
 */
$formatter->format($operation);
```

## Operation failure formatter

An operation failure consists of an operation and optionally an exception.

When an operation failure is formatted, it determines a strategy based on whether
an exception is set.

If an exception is set, the result will be marked with `∴` and uses the exception
message as description. When no exception is present, the result will be marked
with `✔` and uses the formatted operation as description.

An operation failure is formatted using the following pattern:

```
({operationId}){padding} {icon} {description}
```

In order, these show an operation failure with and without exception:

```
(2)      ∴ Could not copy "dist/console" -> "my-app/bin/console".
(1)      ✔ Create directory: "my-app"
```

## Rollback formatter

The rollback formatter can be used to format caught instances of
`TransactionRolledBackException`. 

```php
<?php
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Exception\TransactionRolledBackException;
use Johmanx10\Transaction\Formatter\RollbackFormatter;

/** @var OperationInterface[] $operations */
$transaction = new Transaction(...$operations);

try {
    $transaction->commit();
} catch (TransactionRolledBackException $rollback) {
    $formatter = new RollbackFormatter();
    echo $formatter->format($rollback) . PHP_EOL;
}
```

If the code above tries to process 3 operations, but encounters a problem at the
second operation, the formatted output may look something like:

```
2 operations were rolled back: 6, 2

Stacktrace:
(6)      ∴ Could not copy "dist/console" -> "my-app/bin/console".
(2)      ✔ Create directory: "my-app"
```

This shows that the first operation (2) succeeded and the second operation (6)
failed. At that point the operations were rolled back in reverse order.

See [a working example](examples/file-operations) by running:

```
examples/file-operations
```

## Failed rollback formatter

When operations are rolled back and midway one of the operations breaks on the
rollback, the `FailedRollbackException` will be thrown. It can be formatted
using the failed rollback formatter:

```php
<?php
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Exception\FailedRollbackException;
use Johmanx10\Transaction\Formatter\FailedRollbackFormatter;

/** @var OperationInterface[] $operations */
$transaction = new Transaction(...$operations);

try {
    $transaction->commit();
} catch (FailedRollbackException $rollback) {
    $formatter = new FailedRollbackFormatter();
    echo $formatter->format($rollback) . PHP_EOL;
}
```

When operations `Foo`, `Bar`, `Baz` and `Qux` are executed in order and the
operation breaks at `Qux`, the rollback starts from `Qux` and moves back up.
If the rollback for `Bar` then breaks, the formatted output may look something
like:

```
Failed rolling back operation #5
Operation Bar
Could not rollback Bar.

Previous rollbacks:
(10)     ∴ Failed operation Qux
(8)      ✔ Operation Baz
```

This shows that the operation for `Qux` breaks the chain. `Baz` could be
successfully rolled back, but `Bar` could not and `Foo` is therefore completely
missing from this picture, because a rollback for `Foo` was never attempted.

The exception uses the following format:

```
Failed rolling back operation #{operationId}
{operationDescription}
{rollbackExceptionMessage}
```

And if there have been previous rollbacks, the following is appended:

```

Previous rollbacks:
{previousRollbacks}
```

# Visiting operations

The default implementation of `Transaction` implements the interface
`\Johmanx10\Transaction\Visitor\AcceptingTransactionInterface`, allowing it to
accept operation visitors, implementing
`\Johmanx10\Transaction\Visitor\OperationVisitorInterface`.

This can be used to gather information about operations that are executed during
a transaction commit.

The following shows how to log every operation that is about to be executed
within the transaction:

```php
<?php
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\Visitor\LogOperationVisitor;
use Psr\Log\LoggerInterface;

/** @var LoggerInterface $logger */
$visitor = new LogOperationVisitor($logger);

/** @var OperationInterface[] $operations */
$transaction = new Transaction(...$operations);

$transaction->commit($visitor);
```
