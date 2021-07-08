# Events

The transaction library is built with event dispatching in mind and compatible
with [PSR-14: Event Dispatcher](https://www.php-fig.org/psr/psr-14/).

Some events are merely informational in nature, while others may provide ways
of preventing default behavior or even intercepting parts of the transaction, to
be swapped out dynamically.

Events are split up in three separate groups.

- [Transaction events](#transaction-events)
  - [Operation events](#operation-events), which are triggered inside
    transactions.
- [Rollback events](#rollback-events)

## Default preventable events

Some events implement `\Johmanx10\Transaction\Event\DefaultPreventableInterface`.
This interface dictates that the default behavior corresponding to the event may
be prevented. Either by moving ahead to the next iteration, or by stopping
execution.

E.g.: Before a [stage model](operation.md#staging) is invoked, the event
[StageEvent](#stage-event) is dispatched. When `$event->preventDefault();` is
invoked by any of its listeners, the following becomes true:

- The stage is not invoked
- The stage result event is not dispatched
- The stage is not added to the staging result model

The events that correspond to preventable behavior belong to the following:

| Model                                 | Event                                | Effect of `preventDefault` |
|:--------------------------------------|:-------------------------------------|:---------------------------|
| [Stage](operation.md#staging)         | [StageEvent](#stage-event)           | Skips stage, thus also the corresponding invocation. |
| [Invocation](operation.md#invoking)   | [InvocationEvent](#invocation-event) | Skips invocation. |
| [Rollback](operation.md#rolling-back) | [RollbackEvent](#rollback-event)     | Skips rollback for single invocation. | 

## Transaction events

Transaction level events provide a means to track information about the
transaction, as it is staged and committed.

Transaction level events live in the `\Johmanx10\Transaction\Event` namespace.

### Staging result event

The staging result event is meant to inform the application that staging has
finished. Using its payload, it exposes whether staging was a success.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Event\StagingResultEvent` |
| Payload     | `\Johmanx10\Transaction\Result\StagingResult $result` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Event\TransactionLoggerSubscriber::onAfterStaging`](#logging-subscribers) |

### Commit result event

The commit result event is meant to inform the application that the commit has
finished. Using its payload, it exposes whether the commit was a success. In
case the commit was not a success, and an exception was caught, the exception
is exposed as the reason for the failed commit.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Event\CommitResultEvent` |
| Payload     | `\Johmanx10\Transaction\Result\CommitResult $result` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Event\TransactionLoggerSubscriber::onAfterCommit`](#logging-subscribers) |

## Operation events

Operation level events are meant to:

- Expose information, about the operations, to the application.
- Allow the application to influence their behavior, by:
  - [Preventing default behavior](#default-preventable-events).
  - Intercepting [stage](#stage-event) and [invocation](#invocation-event)
    models before they are invoked.

### Stage event

The stage event is dispatched just before the stage model is invoked. It is
meant to:

- Inform the application a stage is about to be invoked.
- Allow the application to prevent the stage and its corresponding invocation.
- Allow the application to intercept the stage with a replacement stage model.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Operation\Event\StageEvent` |
| Payload     | `\Johmanx10\Transaction\Operation\Stage $stage` |
| Preventable | Yes |
| Example     | [prevent default](../examples/prevent-stage.phpt), [intercept](../examples/intercept-stage.phpt) |

### Stage result event

The stage result event is dispatched just after the stage model is invoked. It
exposes to the application:

- The operation it belongs to
- Whether the operation is staged
- Whether the operation will be invoked

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Operation\Event\StageResultEvent` |
| Payload     | `\Johmanx10\Transaction\Operation\Result\StageResult $result` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber::onAfterStage`](#logging-subscribers) |

### Invocation event

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Operation\Event\InvocationEvent` |
| Payload     | `\Johmanx10\Transaction\Operation\Invocation $invocation` |
| Preventable | Yes |
| Example     | [prevent default](../examples/prevent-invocation.phpt), [intercept](../examples/intercept-invocation.phpt) |

### Invocation result event

The invocation result event is dispatched just after the invocation model is
invoked. It exposes to the application:

- Whether the invocation was invoked
- Whether the invocation was a success
- Any exception caught from the invocation

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Operation\Event\InvocationResultEvent` |
| Payload     | `\Johmanx10\Transaction\Operation\Result\InvocationResult $result` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber::onAfterInvoke`](#logging-subscribers) |

## Rollback events

Rollback level events are meant to:

- Expose information, about the rollback, to the application.
- Allow the application to influence their behavior, by:
  - [Preventing default behavior](#default-preventable-events).
  - Intercepting [rollback](#rollback-event) models before they are invoked.

### Rollback blocked event

At the start of a transaction rollback, a pre-flight test happens to check that:

- The transaction did not successfully commit
- The transaction has not been rolled back before

If any check fails, the rollback block event will be dispatched, after which the
rollback will terminate, always.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Event\RollbackBlockedEvent` |
| Payload     | `bool $rolledBack, bool $committed` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Event\TransactionLoggerSubscriber::onRollbackBlocked`](#logging-subscribers) |

### Rollback event

The rollback event is dispatched just before the rollback model is invoked. It
is meant to:

- Inform the application a rollback is about to be invoked.
- Allow the application to prevent the rollback.
- Allow the application to intercept the rollback with a replacement rollback model.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Operation\Event\RollbackEvent` |
| Payload     | `\Johmanx10\Transaction\Operation\Rollback $rollback`, `?\Throwable $reason` |
| Preventable | Yes |
| Example     | [prevent default](../examples/prevent-rollback.phpt), [intercept](../examples/intercept-rollback.phpt) |

### Rollback result event

The rollback result event is dispatched after all rollback models have been
invoked. It is meant to:

- Inform the application that rolling back has finished successfully.
- Inform the application what rollback models have been invoked.

| Attribute   | Value |
|:------------|:------|
| Class       | `\Johmanx10\Transaction\Event\RollbackResultEvent` |
| Payload     | `array<\Johmanx10\Transaction\Operation\Rollback> $rollbacks` |
| Preventable | No |
| Example     | [`\Johmanx10\Transaction\Event\TransactionLoggerSubscriber::onAfterRollback`](#logging-subscribers) |

# Logging subscribers

To provide equivalent functionality to the
[log operation visitor](upgrade/2.0/log-visitor.md) in the previous major
version, the following event subscribers are available:

- [`\Johmanx10\Transaction\Event\TransactionLoggerSubscriber`](../src/Event/TransactionLoggerSubscriber.php)
- [`\Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber`](../src/Operation/Event/OperationLoggerSubscriber.php)

They require a logger service, compatible with
[PSR-3: Logger Interface](https://www.php-fig.org/psr/psr-3/).

> See the [example implementation](../examples/logging-subscriber.phpt).
