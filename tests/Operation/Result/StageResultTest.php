<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Result;

use Johmanx10\Transaction\Operation\OperationInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Result\StageResult;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Result\StageResult
 */
class StageResultTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            StageResult::class,
            new StageResult(
                staged: true,
                requiresInvoke: true,
                operation: self::createMock(OperationInterface::class)
            )
        );
    }
}
