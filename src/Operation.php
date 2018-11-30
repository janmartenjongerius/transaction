<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe;

class Operation implements DescribableOperationInterface
{
    /** @var callable */
    private $operation;

    /** @var callable|null */
    private $rollback;

    /** @var string */
    private $description;

    /**
     * Constructor.
     *
     * @param callable      $operation
     * @param callable|null $rollback
     * @param string|null   $description
     */
    public function __construct(
        callable $operation,
        callable $rollback = null,
        string $description = null
    ) {
        $this->operation   = $operation;
        $this->rollback    = $rollback;
        $this->description = $description ?? sprintf(
            'Generic operation %s',
            spl_object_hash($this)
        );
    }

    /**
     * Execute the operation.
     *
     * @return void
     */
    public function __invoke(): void
    {
        call_user_func($this->operation);
    }

    /**
     * Apply the rollback mechanism.
     *
     * @return void
     */
    public function rollback(): void
    {
        if ($this->rollback !== null) {
            call_user_func($this->rollback);
        }
    }

    /**
     * Describe the current operation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->description;
    }
}
