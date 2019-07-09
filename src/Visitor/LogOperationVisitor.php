<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Visitor;

use Johmanx10\Transaction\Formatter\OperationFormatter;
use Johmanx10\Transaction\Formatter\OperationFormatterInterface;
use Johmanx10\Transaction\OperationInterface;
use Psr\Log\LoggerInterface;

class LogOperationVisitor implements OperationVisitorInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OperationFormatterInterface */
    private $formatter;

    /**
     * Constructor.
     *
     * @param LoggerInterface             $logger
     * @param OperationFormatterInterface $formatter
     */
    public function __construct(
        LoggerInterface $logger,
        OperationFormatterInterface $formatter = null
    ) {
        $this->logger    = $logger;
        $this->formatter = $formatter ?? new OperationFormatter();
    }

    /**
     * Visit the given operation.
     *
     * @param OperationInterface $operation
     *
     * @return void
     */
    public function __invoke(OperationInterface $operation): void
    {
        $this->logger->info(
            $this->formatter->format($operation)
        );
    }
}
