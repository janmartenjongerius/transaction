<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\Operation\Describable;
use Stringable;

final class Descriptor implements Stringable
{
    use Describable;

    /**
     * Constructor.
     *
     * @param Stringable|string $description
     */
    public function __construct(private Stringable|string $description)
    {
    }
}
