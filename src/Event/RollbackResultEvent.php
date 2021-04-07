<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

use Johmanx10\Transaction\Operation\Rollback;

final class RollbackResultEvent
{
    /** @var Rollback[] */
    public array $rollbacks;

    public function __construct(Rollback ...$rollbacks)
    {
        $this->rollbacks = $rollbacks;
    }
}
