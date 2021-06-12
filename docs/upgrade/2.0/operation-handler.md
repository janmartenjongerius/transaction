# Operation handler

The operation handler functions quite the same. However, instead of attaching
[visitors](custom-visitor.md), it uses [event-dispatching](event-dispatcher.md)
to allow agency over the transaction and its components.

# Improvements

The call `handle` has been renamed to `__invoke` to turn the handler into a
callable.

The `handle` method used to accept the variadic argument
`OperationInterface ...$operations`.

See [Operation handler: Mixed operation types](../../operation-handler.md#mixed-operation-types)
for more information on how this improves expressiveness.

# Additions

The following additions are available on the operation handler, compared to the
previous major version.

- [Custom rollbacks](../../operation-handler.md#custom-rollback) are supported.
- The operation handler returns the
  [commit result](../../transaction.md#commits) from the internal transaction.
