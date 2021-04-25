<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Stage;

final class StageEvent implements DefaultPreventableInterface
{
    use DefaultPreventable;

    public function __construct(public Stage $stage)
    {
    }
}
