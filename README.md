![Packagist Version](https://img.shields.io/packagist/v/johmanx10/transaction?label=@stable)
![PHP from Packagist](https://img.shields.io/packagist/php-v/johmanx10/transaction.svg)
![Packagist](https://img.shields.io/packagist/dt/johmanx10/transaction.png)
![Build status](https://github.com/johmanx10/transaction/actions/workflows/php.yml/badge.svg)
[![codecov](https://codecov.io/gh/johmanx10/transaction/branch/master/graph/badge.svg?token=G7A1FM4W02)](https://codecov.io/gh/johmanx10/transaction)

# Introduction

The library `johmanx10/transaction` allows splitting up scripted operations into
bite sized chunks and execute them in an atomic<sup>1</sup> transaction.

Operations are staged before the transaction is executed. This allows the
transaction to verify if its operations are likely to succeed when invoked.

Operations can also be rolled back. This is done in reverse order of execution.

<small><sup>1</sup> Resistance against power failures is not covered by this
library and is left up to its implementers.</small>

# Installation

```
composer require johmanx10/transaction
```

# Features

- Perform [operations](docs/operation.md):
   - in a [transaction](docs/transaction.md)
   - using an [operation handler](docs/operation-handler.md)
- [Dry-run transactions](docs/transaction.md#dry-runs)
- [Stage operations](docs/operation.md#staging)
- Rollback [transaction](docs/transaction.md#rollbacks) / [operations](docs/operation.md#rolling-back)
- Granular control of behavior
   - Compatible with [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/)
   - [Transaction events](docs/events.md#transaction-events)
   - [Operation events](docs/events.md#operation-events)
- Readily available [logging subscribers](docs/events.md#logging-subscribers)
   - Transaction logging subscriber
   - Operation logging subscriber
   - [PSR-3 logger](https://www.php-fig.org/psr/psr-3/) compatible

# Examples

- [Blocked double rollback](examples/blocked-rollback.phpt)
- [Copy a file](examples/copy-file.phpt)
- [Custom rollback](examples/custom-rollback.phpt)
- [Intercept invoke](examples/intercept-invoke.phpt)
- [Intercept rollback](examples/intercept-rollback.phpt)
- [Intercept stage](examples/intercept-stage.phpt)
- [Logging subscriber](examples/logging-subscriber.phpt)
- [Prevent invoke](examples/prevent-invoke.phpt)
- [Prevent rollback](examples/prevent-rollback.phpt)
- [Prevent stage](examples/prevent-stage.phpt)

# Changes since version 1

[Design goals for version 2](https://github.com/johmanx10/transaction/milestone/3)
have made for significant changes between the major versions.

> - Give userspace more agency (I.e.: do not roll back automatically, to help debugging)
> - Simplify documentation and examples (By reducing complexity of the implementations of calling code)
> - Split up responsibilities of calling code (Caller A only commits, rolling back can be forwarded to caller B)
> - Allow dry-runs by implementing staging functionality
> - Allow userspace to listen for invocation, rollback and staging of operations
> 
> A focus will be made to use PHP 8 features in the new major version.

See: [Upgrading from 1.2.x to 2.0.x](docs/upgrade/upgrade-20.md)

> **N.B.:** This version has feature parity with the previous major version,
> however, features may be implemented differently, causing backward
> incompatibility.

# Supporting version 1.2

For version 1.2, a support branch is kept open. This version will be supported
as long as a
[PHP 7 version is supported](https://www.php.net/supported-versions.php).

This does not include extended support by other vendors! However, if for some
reason the support schedule of PHP changes, this library will follow.

## Active support

A release that is being actively supported.

- Reported bugs are fixed.
- Reported security issues are fixed.
- Releases are only made on an as-needed basis.

Active support ends when the active support of PHP 7.4 ends.

## Security support

A release that is supported for critical security issues only.

- Reported security issues are fixed.
- Releases are only made on an as-needed basis.

Security support ends when the security support of PHP 7.4 ends.
