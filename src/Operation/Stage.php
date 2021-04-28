<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Stringable;

final class Stage implements Stringable
{
    use Describable;

    public function __construct(
        public OperationInterface $operation,
        private Closure $stage
    ) {
        $this->description = $operation;
    }

    public function __invoke(): StageResult
    {
        /** @var ?bool $result */
        $result = ($this->stage)();

        return new StageResult(
            match ($result) {
                StageResult::RESULT_STAGED => true,
                StageResult::RESULT_FAILED, StageResult::RESULT_SKIP => false
            },
            match ($result) {
                StageResult::RESULT_SKIP => false,
                StageResult::RESULT_STAGED, StageResult::RESULT_FAILED => true
            },
            $this->operation
        );
    }
}
