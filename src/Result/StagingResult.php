<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Result;

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;

final class StagingResult
{
    private array $results;

    public function __construct(StageResult ...$results)
    {
        $this->results = $results;
    }

    /**
     * Whether all operations could be successfully staged.
     *
     * @return bool
     */
    public function isStaged(): bool
    {
        return array_reduce(
            $this->results,
            fn (bool $carry, StageResult $result) =>
                $carry && ($result->staged || !$result->requiresInvoke),
            true
        );
    }

    /**
     * @return OperationInterface[]
     */
    public function getRequiredOperations(): iterable
    {
        return array_reduce(
            $this->results,
            function (array $carry, StageResult $result): array {
                if ($result->requiresInvoke) {
                    $carry[] = $result->operation;
                }

                return $carry;
            },
            []
        );
    }
}
