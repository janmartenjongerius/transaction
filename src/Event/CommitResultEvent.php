<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

use Johmanx10\Transaction\Result\CommitResultInterface;

final class CommitResultEvent
{
    public function __construct(public CommitResultInterface $result)
    {
    }
}
