<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Johmanx10\Transaction\Result\CommitResult;
use Johmanx10\Transaction\TransactionFactoryInterface;

final class OperationHandler implements OperationHandlerInterface
{
    private ?Closure $rollback = null;

    public function __construct(private TransactionFactoryInterface $factory)
    {
    }

    public function __invoke(
        OperationInterface | iterable ...$operations
    ): CommitResult {
        $transaction = $this->factory->__invoke(
            ...self::flatten(...$operations)
        );

        $result = $transaction->commit($this->rollback);

        if (!$result->committed()) {
            $result->rollback();
        }

        return $result;
    }

    private static function flatten(
        OperationInterface | iterable ...$operations
    ): iterable {
        foreach ($operations as $children) {
            if (!is_iterable($children)) {
                yield $children;
                continue;
            }

            foreach ($children as $operation) {
                yield $operation;
            }
        }
    }

    public function withRollback(callable $rollback): static
    {
        $handler = clone $this;
        $handler->rollback = fn () => $rollback();
        return $handler;
    }
}
