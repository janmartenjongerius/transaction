<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Operation\Result\InvocationResult;

final class InvocationResultEvent
{
    public function __construct(public InvocationResult $result)
    {
    }
}
