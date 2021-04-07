<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

/**
 * @todo change to traits. It may be necessary to retire existing traits.
 */
abstract class AbstractOperation implements OperationInterface
{
    public function stage(): Stage
    {
        return new Stage(
            $this,
            fn () => $this->stageOperation()
        );
    }

    abstract protected function stageOperation(): ?bool;

    public function __invoke(): Invocation
    {
        return new Invocation(
            $this,
            fn () => $this->run(),
            fn () => $this->rollback()
        );
    }

    abstract protected function run(): ?bool;
    abstract protected function rollback(): void;
}
