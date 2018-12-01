<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

interface OperationInterface
{
    /**
     * Execute the operation.
     *
     * @return void
     */
    public function __invoke(): void;

    /**
     * Apply the rollback mechanism.
     *
     * @return void
     */
    public function rollback(): void;
}
