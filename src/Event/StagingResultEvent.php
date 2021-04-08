<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

use Johmanx10\Transaction\Result\StagingResult;

final class StagingResultEvent
{
    public function __construct(public StagingResult $result)
    {
    }
}
