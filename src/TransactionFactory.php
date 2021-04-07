<?php
declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Operation\OperationInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class TransactionFactory implements TransactionFactoryInterface
{
    public const STRATEGY_EXECUTE = true;
    public const STRATEGY_DRY_RUN = false;

    public function __construct(
        private ?EventDispatcherInterface $dispatcher = null,
        private bool $strategy = self::STRATEGY_EXECUTE
    ) {}

    /**
     * Create a new transaction for the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return TransactionInterface
     */
    public function __invoke(
        OperationInterface ...$operations
    ): TransactionInterface {
        $transaction = $this->strategy === self::STRATEGY_DRY_RUN
            ? new DryRun(...$operations)
            : new Transaction(...$operations);

        $transaction->setDispatcher($this->dispatcher);

        return $transaction;
    }
}
