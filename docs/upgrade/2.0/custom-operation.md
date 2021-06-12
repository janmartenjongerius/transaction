# Custom operations

A big part of implementing this library, is using
[custom operations](../../operation.md#custom-operation).

To make custom operations compatible with the current version, follow this guide
for every operation.

## Previous interfaces

The previous interfaces were:

```php
interface OperationInterface
{
    /**
     * Execute the operation.
     *
     * @return void
     */
    public function __invoke(): void;

    /**
     * Apply the rollback mechanism.
     *
     * @return void
     */
    public function rollback(): void;
}
```

And optionally, to make them "describable":

```php
interface DescribableOperationInterface extends OperationInterface
{
    /**
     * Describe the current operation.
     *
     * @return string
     */
    public function __toString(): string;
}
```

Since PHP 8 provides the `Stringable` interface, the
`DescribableOperationInterface` has become obsolete.

## Current interfaces

Currently, an operation implements the following interface:

```php
interface OperationInterface extends Stringable
{
    public function stage(): Stage;

    public function __invoke(): Invocation;
}
```

## Changes

- Making an operation describable is now mandatory
- An operation is now staged before being invoked
- Operations aren't immediately callable. Instead, they produce callables for
   - Staging
   - Invocation
      - Rolling back

## Upgrading

The following steps should upgrade your previous implementation to a compatible
current implementation:

1. Replace `Johmanx10\Transaction\DescribableOperationInterface`
   and `Johmanx10\Transation\OperationInterface`
   with `Johmanx10\Transation\Operation\OperationInterface`
2. Use the following traits:
   - `Johmanx10\Transaction\Operation\Stageable`
   - `Johmanx10\Transaction\Operation\Invokable`
   > OR: `Johmanx10\Transaction\Operation\Operable` (combined of the above)
3. Rename `public __invoke(): void` to `protected run(): ?bool`
   > Optionally: if `false` is returned, this indicates that the operation has failed.
   > Returning `false` is a good alternative to throwing an exception, unless the
   > exception adds valuable context to a problem.
   > 
   > Returning `null` or `true` keeps the previous behavior.
4. Implement the method to [stage](../../operation.md#staging) an operation.
5. Make sure the operation is describable by doing one of:
   - Use `Johmanx10\Transaction\Operation\Describable` and fill the
     `$description` property.
   - Implement the `__toString(): string` method.
