<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Johmanx10\Transaction\Result\CommitResult;
use Johmanx10\Transaction\TransactionFactoryInterface;

final class OperationHandler implements OperationHandlerInterface
{
    public function __construct(private TransactionFactoryInterface $factory) {}

    public function __invoke(
        OperationInterface|iterable ...$operations
    ): CommitResult {
        $transaction = $this->factory->__invoke(
            ...self::flatten(...$operations)
        );

        $result = $transaction->commit();

        if (!$result->committed()) {
            $result->rollback();
        }

        return $result;
    }

    private static function flatten(
        OperationInterface|iterable ...$operations
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
}
