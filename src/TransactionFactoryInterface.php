<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

interface TransactionFactoryInterface
{
    /**
     * Create a transaction for the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return TransactionInterface
     */
    public function createTransaction(
        OperationInterface ...$operations
    ): TransactionInterface;
}
