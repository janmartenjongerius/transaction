<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

interface DefaultPreventableInterface
{
    /**
     * Whether the default behavior associated to the event is prevented.
     *
     * @return bool
     */
    public function isDefaultPrevented(): bool;

    /**
     * Prevent the default behavior associated with the current event.
     */
    public function preventDefault(): void;
}
