<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Result;

use Johmanx10\Transaction\DispatcherAware;
use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Event\RollbackResultEvent;
use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Throwable;

final class CommitResult
{
    use DispatcherAware;

    private array $results;
    private bool $rolledBack = false;
    private bool $committed;

    public function __construct(
        public StagingResult $staging,
        InvocationResult ...$results
    ) {
        $this->results = $results;
    }

    /**
     * Confirm whether all operations were successful.
     *
     * @return bool
     */
    public function committed(): bool
    {
        return $this->committed ??= array_reduce(
            $this->results,
            fn (bool $carry, InvocationResult $result) =>
                $carry && $result->success,
            $this->staging->isStaged()
        );
    }

    /**
     * Get the exception that caused the transaction not to commit, or null if
     * the transaction succeeded, or the disruption was not caused by an exception.
     *
     * @return Throwable|null
     */
    public function getReason(): ?Throwable
    {
        return array_reduce(
            $this->results,
            fn (?Throwable $carry, InvocationResult $result) =>
                $carry ?? $result->exception
        );
    }

    /**
     * Roll back the operations that weren't skipped.
     *
     * @param callable|null $rollback
     */
    public function rollback(callable $rollback = null): void
    {
        if ($this->rolledBack || $this->committed()) {
            $this->dispatch(
                new RollbackBlockedEvent(
                    rolledBack: $this->rolledBack,
                    committed: $this->committed()
                )
            );
            return;
        }

        $this->rolledBack = true;
        $rollbacks = [];

        foreach ($this->getResults($rollback) as $result) {
            if (!$result->invoked) {
                continue;
            }

            $event = new RollbackEvent($result->rollback(), $result->exception);
            $this->dispatch($event);
            $rollback = $event->rollback;

            if ($event->isDefaultPrevented()) {
                continue;
            }

            $rollback();

            $rollbacks[] = $rollback;
        }

        $this->dispatch(new RollbackResultEvent(...$rollbacks));
    }

    /**
     * @param callable|null $rollback
     *
     * @return InvocationResult[]
     */
    private function getResults(?callable $rollback): iterable
    {
        if ($rollback !== null) {
            yield new InvocationResult(
                'Transaction',
                $this->committed(),
                true,
                $this->getReason(),
                $rollback
            );
            return;
        }

        yield from array_reverse($this->results);
    }
}
