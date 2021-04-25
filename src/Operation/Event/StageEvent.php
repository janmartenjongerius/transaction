<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\Stage;
use Stringable;

final class StageEvent implements DefaultPreventableInterface, Stringable
{
    use DefaultPreventable;
    use Describable;

    public function __construct(public Stage $stage)
    {
        $this->description = $stage;
    }
}
