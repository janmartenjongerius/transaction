<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Visitor\OperationVisitorAwareInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use Johmanx10\Transaction\Visitor\TransactionFactory;
use Johmanx10\Transaction\Visitor\TransactionFactoryInterface;

class OperationHandler implements
    OperationHandlerInterface,
    OperationVisitorAwareInterface
{
    /** @var OperationVisitorInterface[] */
    private $visitors = [];

    /** @var TransactionFactoryInterface */
    private $factory;

    /**
     * Constructor.
     *
     * @param TransactionFactoryInterface|null $factory
     */
    public function __construct(TransactionFactoryInterface $factory = null)
    {
        $this->factory = $factory ?? new TransactionFactory();
    }

    /**
     * Handle the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     */
    public function handle(OperationInterface ...$operations): void
    {
        $this
            ->factory
            ->createTransaction(...$operations)
            ->commit(...array_values($this->visitors));
    }

    /**
     * Attach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function attachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            $this->visitors[spl_object_hash($visitor)] = $visitor;
        }
    }

    /**
     * Detach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function detachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            unset($this->visitors[spl_object_hash($visitor)]);
        }
    }
}
