<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Visitor\OperationVisitorAwareInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;

class OperationHandler implements
    OperationHandlerInterface,
    OperationVisitorAwareInterface
{
    /** @var OperationVisitorInterface[] */
    private $visitors = [];

    /**
     * Handle the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     */
    public function handle(OperationInterface ...$operations): void
    {
        $transaction = new Transaction(...$operations);
        $transaction->commit(...array_values($this->visitors));
    }

    /**
     * Attach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function attachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            $this->visitors[spl_object_hash($visitor)] = $visitor;
        }
    }

    /**
     * Detach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function detachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            unset($this->visitors[spl_object_hash($visitor)]);
        }
    }
}
