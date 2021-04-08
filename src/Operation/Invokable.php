<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

trait Invokable
{
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
