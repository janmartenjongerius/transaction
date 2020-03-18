<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Visitor;

interface OperationVisitorAwareInterface
{
    /**
     * Attach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function attachVisitor(OperationVisitorInterface ...$visitors): void;

    /**
     * Detach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function detachVisitor(OperationVisitorInterface ...$visitors): void;
}
