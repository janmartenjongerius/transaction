<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Operation\Result\StageResult;

final class StageResultEvent
{
    public function __construct(public StageResult $result) {}
}
