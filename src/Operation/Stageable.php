<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use LogicException;

trait Stageable
{
    private ?Closure $stage;

    /**
     * @return Stage
     *
     * @throws LogicException When the trait is used by a class that does not
     *   implement OperationInterface.
     */
    public function stage(): Stage
    {
        if (!$this instanceof OperationInterface) {
            throw new LogicException(
                sprintf(
                    'Trait %s can only be used by instance of %s',
                    __CLASS__,
                    OperationInterface::class
                )
            );
        }

        return new Stage(
            $this,
            $this->stage ?? fn () => true
        );
    }
}
