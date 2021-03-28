<?php


namespace Johmanx10\Transaction;

interface OperationHandlerInterface
{
    /**
     * Handle the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     */
    public function handle(OperationInterface ...$operations): void;
}
