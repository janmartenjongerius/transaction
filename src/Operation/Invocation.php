<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Stringable;
use Throwable;

final class Invocation implements Stringable
{
    use Describable;

    public function __construct(
        private Stringable|string $description,
        private Closure $operation,
        private Closure $rollback
    ) {
    }

    public function __invoke(): InvocationResult
    {
        $success = true;
        $exception = null;

        try {
            $result = ($this->operation)();

            if (is_bool($result)) {
                $success = $result;
            }
        } catch (Throwable $exception) {
            $success = false;
        }

        return new InvocationResult(
            $this,
            $success && $exception === null,
            true,
            $exception,
            $this->rollback
        );
    }
}
