<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\OperationHandler;

/**
 * @coversDefaultClass \Johmanx10\Transaction\OperationHandler
 */
class OperationHandlerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::handle
     * @covers ::attachVisitor
     * @covers ::detachVisitor
     */
    public function testHandler(): void
    {
        $visitorA = new class implements OperationVisitorInterface
        {
            /** @var int */
            public $numInvocations = 0;

            /**
             * @param OperationInterface $operation
             *
             * @return void
             */
            public function __invoke(OperationInterface $operation): void
            {
                ++$this->numInvocations;
            }
        };

        $visitorB = clone $visitorA;

        $subject = new OperationHandler();
        $subject->attachVisitor($visitorA, $visitorB);

        $operations = [
            $this->createMock(OperationInterface::class),
            $this->createMock(OperationInterface::class),
            $this->createMock(OperationInterface::class)
        ];

        $subject->handle(...$operations);

        $this->assertEquals(3, $visitorA->numInvocations);
        $this->assertEquals(3, $visitorB->numInvocations);

        $subject->detachVisitor($visitorA);
        $subject->handle(...$operations);

        $this->assertEquals(3, $visitorA->numInvocations);
        $this->assertEquals(6, $visitorB->numInvocations);
    }
}
