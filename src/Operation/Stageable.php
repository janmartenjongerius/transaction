<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use RuntimeException;

trait Stageable
{
    /**
     * @return Stage
     *
     * @throws RuntimeException When the using class does not implement OperationInterface
     */
    public function stage(): Stage
    {
        if (!$this instanceof OperationInterface) {
            throw new RuntimeException(
                sprintf(
                    'Trait %s can only be implemented by implementer of %s',
                    self::class,
                    OperationInterface::class
                )
            );
        }

        return new Stage(
            $this,
            fn () => $this->stageOperation()
        );
    }

    abstract protected function stageOperation(): ?bool;
}
