<?php


namespace Johmanx10\Transaction\Visitor;

use Johmanx10\Transaction\OperationInterface;

interface TransactionFactoryInterface
{
    /**
     * Create a transaction for the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return AcceptingTransactionInterface
     */
    public function createTransaction(
        OperationInterface ...$operations
    ): AcceptingTransactionInterface;
}
