<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Operation\Invocation;

final class InvocationEvent
{
    public function __construct(public Invocation $invocation)
    {
    }
}
