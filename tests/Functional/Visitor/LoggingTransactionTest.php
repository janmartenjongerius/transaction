<?php


namespace Johmanx10\Transaction\Tests\Functional\Visitor;

use Johmanx10\Transaction\Operation;
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\Visitor\LogOperationVisitor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;

class LoggingTransactionTest extends TestCase
{
    /**
     * @return void
     * @coversNothing
     */
    public function testLogVisitorLogsOperationsInTransaction(): void
    {
        $logger      = new TestLogger();
        $visitor     = new LogOperationVisitor($logger, null, LogLevel::EMERGENCY);
        $description = 'Performing operation within ' . __METHOD__;
        $transaction = new Transaction(
            new Operation(
                function () {
                },
                function () {
                },
                $description
            )
        );

        $transaction->commit($visitor);

        $this->assertTrue(
            $logger->hasRecord($description, LogLevel::EMERGENCY),
            'The logger receives a description of the operation to be executed.'
        );
    }
}
