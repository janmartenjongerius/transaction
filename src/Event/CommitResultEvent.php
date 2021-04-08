<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

use Johmanx10\Transaction\Result\CommitResult;

final class CommitResultEvent
{
    public function __construct(public CommitResult $result)
    {
    }
}
