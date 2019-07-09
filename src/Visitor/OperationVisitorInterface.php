<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Visitor;

use Johmanx10\Transaction\OperationInterface;

interface OperationVisitorInterface
{
    /**
     * Visit the given operation.
     *
     * @param OperationInterface $operation
     *
     * @return void
     */
    public function __invoke(OperationInterface $operation): void;
}
