<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

trait DefaultPreventable
{
    private bool $defaultPrevented = false;

    /**
     * Whether the default behavior associated to the event is prevented.
     *
     * @return bool
     */
    public function isDefaultPrevented(): bool
    {
        return $this->defaultPrevented;
    }

    /**
     * Prevent the default behavior associated with the current event.
     */
    public function preventDefault(): void
    {
        $this->defaultPrevented = true;
    }
}
