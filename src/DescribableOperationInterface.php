<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe;

interface DescribableOperationInterface extends OperationInterface
{
    /**
     * Describe the current operation.
     *
     * @return string
     */
    public function __toString(): string;
}
