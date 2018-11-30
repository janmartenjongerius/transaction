<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe;

interface PipeInterface
{
    /**
     * Invoke the operations in order.
     * Roll back operations in reverse order, from the point where a throwable
     * was caught.
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     */
    public function __invoke(OperationInterface ...$operations): void;
}
