<?php
declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Operation\OperationInterface;

interface TransactionFactoryInterface
{
    /**
     * Create a new transaction for the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return TransactionInterface
     */
    public function __invoke(
        OperationInterface ...$operations
    ): TransactionInterface;
}
