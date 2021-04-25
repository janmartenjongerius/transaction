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
            ...array_reduce(
                $operations,
                function (
                    array $carry,
                    OperationInterface $operation
                ): array {
                    $stage = $operation->stage();
                    $event = new StageEvent($stage);

                    $this->dispatch($event);

                    if (!$event->isDefaultPrevented()) {
                        $result = $stage();
                        $this->dispatch(new StageResultEvent($result));
                        $carry[] = $result;
                    }

                    return $carry;
                },
                []
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
            $event = new InvocationEvent($invocation);

            $this->dispatch($event);

            if ($event->isDefaultPrevented()) {
                continue;
            }

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
