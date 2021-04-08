<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;

trait Invokable
{
    private Closure $invocation;
    private ?Closure $rollback;

    public function __invoke(): Invocation
    {
        return new Invocation(
            $this,
            $this->invocation,
            $this->rollback ?? fn () => true
        );
    }
}
