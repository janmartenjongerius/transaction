<?php

declare(strict_types=1);

namespace Johmanx10\Transaction;

use Psr\EventDispatcher\EventDispatcherInterface;

trait DispatcherAware
{
    private ?EventDispatcherInterface $dispatcher;

    public function setDispatcher(?EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch the given event, if the internal dispatcher is set.
     *
     * @param object $event
     */
    private function dispatch(object $event): void
    {
        $this->dispatcher?->dispatch($event);
    }
}
