<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests\Exception;

use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Exception\OperationException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Exception\OperationException
 */
class OperationExceptionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getOperation
     */
    public function testException(): void
    {
        $subject = new OperationException(
            'Foo',
            $this->createMock(OperationInterface::class)
        );
        $this->assertInstanceOf(OperationException::class, $subject);
        $this->assertInstanceOf(OperationInterface::class, $subject->getOperation());

        $subject = new OperationException('Foo', null);
        $this->assertInstanceOf(OperationException::class, $subject);
        $this->assertNull($subject->getOperation());
    }
}
