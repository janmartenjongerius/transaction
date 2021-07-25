# Operation

An operation is a model that describes and facilitates the execution of an
atomic piece of code.

It consists of:

- a description
- a stage
- an invocation
- a rollback

## Staging

While staging an operation, the `stage` is invoked on the operation, to create a
new stage model.

The stage model can then be invoked to determine whether the operation can be
executed successfully.

Return values of the stage invocation are one of:

| Constant                     | Effect |
|:-----------------------------|:-------|
| `StageResult::RESULT_STAGED` | The operation will be invoked. |
| `StageResult::RESULT_FAILED` | The operation cannot be invoked, the transaction will not be committed. |
| `StageResult::RESULT_SKIP`   | The operation will not be invoked, the transaction continues. |

Where `StageResult` is `\Johmanx10\Transaction\Operation\Result\StageResult`.

> See [events](events.md) for more information on how to use specific events.

## Invoking

While invoking an operation, the `__invoke` method is called on the operation,
to create a new invocation model.

The invocation model consists of:

- description
- operation callable
- rollback callable

The invocation will then be invoked, to run the current "operation" of the
transaction. Return values and exceptions thrown in this callable are important.

The invocation model itself wraps around the call. After invocation, it returns
an invocation result model, consisting of:

- description
- success flag
- invoked flag
- exception, optionally
- rollback callable

The `invoked` flag determines whether the invocation was actually invoked. If an
operation was previously marked to be skipped, or an earlier operation did not
succeed, this flag is set to `false` and the operation callable will not have
been called.

Whether `success` is set to `true`, can be determined using the following truth
table:

| Return value    | Is a boolean value? | Exception occurred? | Success |
|:----------------|:--------------------|:--------------------|:--------|
| `string(1) "Y"` | No                  | No                  | `true`  |
| `bool(true)`    | Yes                 | No                  | `true`  |
| `bool(false)`   | Yes                 | No                  | `false` |
| `int(1)`        | No                  | No                  | `true`  |
| `int(0)`        | No                  | No                  | `true`  |
| `NULL`          | No                  | No                  | `true`  |
| N/A             | N/A                 | Yes                 | `false` |

The reason behind allowing boolean values to dictate success is to simplify
inline operations with short closures, like:

```php
use Johmanx10\Transaction\Operation\Operation;

$operation = new Operation(
    description: 'Create project root',
    invocation: fn () => mkdir(
        directory: $argv[1] ?? '/dev/null', 
        recursive: true
    )
);
```

The return value of `mkdir`, like many PHP standard functions, is a boolean, or
at least returns `false` on failure. This perfectly aligns with failing the
operation when it encounters `false`.

A more complete example would be:

```php
use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Result\StageResult;

$projectRoot = $argv[1];

$operation = new Operation(
    description: 'Create project root',
    invocation: fn () => mkdir(
        directory: $projectRoot,
        recursive: true
    ),
    rollback: fn () => rmdir($projectRoot) or throw new RuntimeException(
        sprintf('Could not remove directory: "%s"', $projectRoot)
    ),
    stage: fn () => file_exists($projectRoot)
        // A file exists where the project root should go.
        ? (
            is_dir($projectRoot)
                // It already is a directory, so the operation can be skipped.
                ? StageResult::RESULT_SKIP
                // It is not a directory, so the transaction should not commit.
                : StageResult::RESULT_FAILED
        )
        // The file does not exist, continue as planned.
        : StageResult::RESULT_STAGED
);
```

### Rolling back

After an invocation is invoked, the invocation result gets forwarded the
rollback callable.

Whenever a commit is performed on a transaction, the commit result contains all
invocation results. Of the invocation results that are marked as invoked, the
commit result may perform a rollback.

> See [transaction rollbacks](transaction.md#rollbacks) for more information on
> the inner workings of a transaction rollback.

It is possible that the rollback mechanism may be skipped, in favor of a
transaction wide rollback.

> See [operation handler with custom rollback](operation-handler.md#custom-rollback)
> for more information on transaction wide rollbacks.

It is important to note that the callable in a rollback model is treated as if
the return type is void. In order to interrupt the rollback mechanism, because
of a malfunctioning rollback, it is necessary to throw an error or exception.
The rollback mechanism will not catch this, which means it bubbles up to
userland:

```php
/** @var \Johmanx10\Transaction\Result\CommitResultInterface $commitResult */

try {
    $commitResult->rollback();
} catch (\Throwable $exception) {
    // Handle failing rollbacks.
}
```

# Custom operation

Although the word custom can easily be associated with exceptions to the rule,
it is highly encouraged to create custom operations for your application.

This library focuses on the abstraction of performing transactions through code.
It is the domain layer that is responsible for translating this library to code
that is recognizable by your application and its maintainers.

Under [`examples/src`](../examples/src) are a couple of example implementations
of custom operations. Those show off examples of filesystem operations. However,
this library does not limit the user in its implementations. I.e.:

- GIT operations, to split a monorepo, tag a new release or push changes after tagging
- Simulate a workflow, or perform an end-to-end test, to assert its behavior
- A nightly build process, that builds artifacts, tags and deploys them automatically

## Traits

To simplify implementing custom operations, the following traits are provided.
It is strongly encouraged to use the traits, as this takes away the need to
implement logic that is best left up to the library.

The traits live in `Johmanx10\Transaction\Operation\`

| Trait         | Description |
|:--------------|:------------|
| `Stageable`   | Reduce construction of Stage model to implementing a callable method. |
| `Invokable`   | Reduce construction of Invocation model to implementing callable methods for invocation and rollback. |
| `Operable`    | Combine `Stageable` and `Invokable`. |
| `Describable` | Simplify making the operation describable to just setting the `$description` property. |

> The `$description` property accepts `Stringable | string`.
> The `Operation`, `Invocation`, `Stage` and `Rollback` models, as well as the
> `OperationInterface` all implement / extend `Stringable` and pass on their
> description down the line. This means that any of the models can be cast to
> string, making them will expose the same description.
>
> Because they are all `Stringable`, the conversion to string is deferred to
> the moment when it is requested to describe itself.

It may be useful for custom operations to skip using the `Describable` trait.
At that point, the interface forces the implementation of `__toString(): string`.
This opens up the ability to defer creating a message with dynamic information.

## Example custom operation

The following is an example of a custom operation. It is not guaranteed to work.

```php
<?php

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Operable;
use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\Result\StageResult;

final class GzipFile implements OperationInterface
{
    use Operable;
    use Describable;
    
    public function __construct(
        private string $filename, 
        private string|Stringable $content,
        private string $mode = 'wb9'
    ) {
        $this->description = sprintf(
            'Archive "%s" using mode "%s"', 
            $filename, 
            $mode
        );
    }
    
    protected function stageOperation(): ?bool
    {
        if (!extension_loaded('zlib') || !function_exists('gzopen')) {
            // Missing required PHP extension and function.
            // Abort the transaction.
            return StageResult::RESULT_FAILED;
        }
    
        return file_exists($this->filename)
            // The archive already exists. Delete or rename it first.
            ? StageResult::RESULT_FAILED
            // The archive needs to be made. Proceed as planned.
            : StageResult::RESULT_STAGED;
    }
    
    protected function run(): ?bool
    {
        $archive = gzopen(filename: $this->filename, mode: $this->mode);
        gzwrite(stream: $archive, data: (string)$this->content);
        gzclose($archive);
    }
    
    protected function rollback(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename) or throw new RuntimeException(
                sprintf('Could not delete archive "%s"', $this->filename)
            );
        }
    }
}
```

This operation does the following:

1. Stage:
   - If platform requirements are missing, prevent the transaction
   - If the archive was previously made, prevent the transaction
   - Ctherwise, continue
2. Invoke:
   - Create an archive with the given filename, using the configured mode
   - Write the contents to the archive
   - Close the archive
3. Rollback:
   If (part of) the transaction failed, and the archive was made, delete it.
   
**N.B.:** This operation demonstrates a reason why it might be beneficial to not use the
`Describable` trait. Consider that `$content` is an object that proxies a file
of several gigabytes of data. Loading in this data should be done at the last
possible moment, to prevent using resources at the wrong time.

However, perhaps it is valuable to add to the description how many bytes are
compressed into the archive.

```php
use Describable;

public function __construct(
    private string $filename, 
    private string|Stringable $content,
    private string $mode = 'wb9'
) {
    $this->description = sprintf(
        'Archive %d bytes into "%s" using mode "%s"',
        strlen((string)$content),
        $filename, 
        $mode
    );
}
```

Doing that loads in 2 gigabytes of file data when the operation is prepared.
It might be the operation will never be invoked at all. That is terrible on
performance.

Now, without the `Describable` trait:

```php
public function __construct(
    private string $filename, 
    private string|Stringable $content,
    private string $mode = 'wb9'
) {}

public function __toString(): string
{
    return sprintf(
        'Archive %d bytes into "%s" using mode "%s"',
        strlen((string)$this->content),
        $this->filename, 
        $thid->mode
    );
}
```

While this exposes the same information, the file is not loaded when not needed.
