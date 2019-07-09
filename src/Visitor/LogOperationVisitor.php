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
use Psr\Log\LogLevel;

class LogOperationVisitor implements OperationVisitorInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OperationFormatterInterface */
    private $formatter;

    /** @var string */
    private $logLevel;

    /**
     * Constructor.
     *
     * @param LoggerInterface             $logger
     * @param OperationFormatterInterface $formatter
     * @param string                      $logLevel
     */
    public function __construct(
        LoggerInterface $logger,
        OperationFormatterInterface $formatter = null,
        string $logLevel = LogLevel::INFO
    ) {
        $this->logger    = $logger;
        $this->formatter = $formatter ?? new OperationFormatter();
        $this->logLevel  = $logLevel;
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
        $this->logger->log(
            $this->logLevel,
            $this->formatter->format($operation)
        );
    }
}
