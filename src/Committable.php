<?php

declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Event\CommitResultEvent;
use Johmanx10\Transaction\Event\StagingResultEvent;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Event\InvocationResultEvent;
use Johmanx10\Transaction\Operation\Event\StageEvent;
use Johmanx10\Transaction\Operation\Event\StageResultEvent;
use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\Result\CommitResult;
use Johmanx10\Transaction\Result\StagingResult;

trait Committable
{
    use DispatcherAware;

    /** @var array|OperationInterface[] */
    private array $operations;

    private function stage(OperationInterface ...$operations): StagingResult
    {
        $result = new StagingResult(
            ...array_map(
                function (OperationInterface $operation): StageResult {
                    $stage = $operation->stage();

                    $this->dispatch(new StageEvent($stage));

                    $result = $stage();

                    $this->dispatch(new StageResultEvent($result));

                    return $result;
                },
                $operations
            )
        );

        $this->dispatch(new StagingResultEvent($result));

        return $result;
    }

    public function commit(): CommitResult
    {
        $results = [];
        $staging = $this->stage(...$this->operations);
        $skip = !$staging->isStaged();

        foreach ($staging->getRequiredOperations() as $operation) {
            $invocation = $operation();

            $this->dispatch(new InvocationEvent($invocation));

            $result = $skip
                ? new InvocationResult(
                    $invocation,
                    false,
                    false,
                    null,
                    fn () => false
                )
                : $this->invoke($invocation);

            $skip = $skip || !$result->success;

            $this->dispatch(new InvocationResultEvent($result));

            $results[] = $result;
        }

        $result = new CommitResult($staging, ...$results);
        $result->setDispatcher($this->dispatcher);

        $this->dispatch(new CommitResultEvent($result));

        return $result;
    }

    abstract private function invoke(Invocation $invocation): InvocationResult;
}
