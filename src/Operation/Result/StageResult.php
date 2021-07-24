<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Result;

use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\OperationInterface;
use Stringable;

final class StageResult implements Stringable
{
    use Describable;

    public const RESULT_STAGED = true;
    public const RESULT_FAILED = false;
    public const RESULT_SKIP = null;

    public function __construct(
        public bool $staged,
        public bool $requiresInvoke,
        public OperationInterface $operation
    ) {
        $this->description = $operation;
    }
}
