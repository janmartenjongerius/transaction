# Upgrading from 1.2.x to 2.0.x

This new major version brings with it a number of [new features](#new-features)
and [incompatibilities](#backward-incompatible-changes).

Make sure to pay attention to the
[backward incompatible changes](#backward-incompatible-changes), if your
application now works with the previous major version of this library.

> **N.B.:** This version has equivalent functionality for all features in the
> previous major version. However, features may be implemented differently,
> causing backward incompatibility.

## New features

Some new features have been added, mostly to give the implementer more agency
and granularity in their control mechanisms.

Before diving into the more concrete upgrade guides, it is advised to read up on
new features, to make a better informed decision on how to upgrade your current
implementation.

- [Custom rollbacks](2.0/operation-handler.md)
- [Staging](2.0/transaction.md#staging)
- [Dry-runs](2.0/transaction.md#dry-runs)
- [Event dispatcher](2.0/event-dispatcher.md)

## Backward incompatible changes

While this new version has equivalent functionality for all features in the
previous version, some features are implemented differently.

The following describe how to upgrade your current implementation:

- [Automatic rollbacks](2.0/transaction.md#rollbacks)
- [Operation](2.0/custom-operation.md)
- [Operation visitors](2.0/custom-visitor.md)
- [Exception handling](2.0/exceptions.md)
- [Logging operations](2.0/log-visitor.md)
