<?php


namespace Johmanx10\Transaction;

interface DescribableOperationInterface extends OperationInterface
{
    /**
     * Describe the current operation.
     *
     * @return string
     */
    public function __toString(): string;
}
