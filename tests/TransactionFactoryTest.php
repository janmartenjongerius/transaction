<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\TransactionInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\TransactionFactory;

/**
 * @coversDefaultClass \Johmanx10\Transaction\TransactionFactory
 */
class TransactionFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::createTransaction
     */
    public function testCreateTransaction(): void
    {
        $subject = new TransactionFactory();

        $this->assertInstanceOf(
            TransactionInterface::class,
            $subject->createTransaction(),
            'Transaction can be made without operations.'
        );

        $this->assertInstanceOf(
            TransactionInterface::class,
            $subject->createTransaction(
                $this->createMock(OperationInterface::class)
            ),
            'Transaction can be made with a single operation.'
        );

        $this->assertInstanceOf(
            TransactionInterface::class,
            $subject->createTransaction(
                $this->createMock(OperationInterface::class),
                $this->createMock(OperationInterface::class),
                $this->createMock(OperationInterface::class)
            ),
            'Transaction can be made with multiple operations.'
        );
    }
}
