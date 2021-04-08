<?php

declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;

final class Transaction implements TransactionInterface
{
    use Committable;

    public function __construct(OperationInterface ...$operations)
    {
        $this->operations = $operations;
    }

    private function invoke(Invocation $invocation): InvocationResult
    {
        return $invocation();
    }
}
