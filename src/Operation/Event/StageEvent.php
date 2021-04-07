<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Operation\Stage;

final class StageEvent
{
    public function __construct(public Stage $stage) {}
}
