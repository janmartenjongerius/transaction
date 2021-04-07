<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Stringable;

interface OperationInterface extends Stringable
{
    public function stage(): Stage;

    public function __invoke(): Invocation;
}
