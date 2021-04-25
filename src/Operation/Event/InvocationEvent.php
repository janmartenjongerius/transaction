<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\Invocation;
use Stringable;

final class InvocationEvent implements DefaultPreventableInterface, Stringable
{
    use DefaultPreventable;
    use Describable;

    public function __construct(public Invocation $invocation)
    {
        $this->description = $invocation;
    }
}
