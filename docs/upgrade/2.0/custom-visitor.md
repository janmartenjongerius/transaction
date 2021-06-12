# Custom visitors

The visitor concept has been completely replaced by 
[event dispatching](event-dispatcher.md).

To create re-apply existing visitors to the new pattern, convert them to a
listener for the following event:

`\Johmanx10\Transaction\Operation\Event\InvocationEvent`

However, between versions, the focus has shifted to give you as implementor more
agency over the process. There are now multiple [events](../../events.md) and
corresponding control mechanisms to influence the process of commits, staging
and rollbacks.

> See also the migration guide for the [log visitor](log-visitor.md).
