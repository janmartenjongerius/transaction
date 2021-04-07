<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Stringable;

trait Describable
{
    private Stringable|string $description;

    public function __toString(): string
    {
        return (string)$this->description;
    }
}
