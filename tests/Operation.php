<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Stage;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Stringable;

trait Operation
{
    abstract protected function createMock(string $originalClassName): MockObject;

    private function createOperation(
        ?bool $willStage = true,
        bool $willRun = true,
        bool $willRollback = false,
        Stringable|string $description = ''
    ): OperationInterface {
        /** @var OperationInterface|MockObject $operation */
        $operation = $this->createMock(OperationInterface::class);

        $operation
            ->expects(self::any())
            ->method('stage')
            ->willReturn(
                $this->createStage($operation, $willStage)
            );

        $operation
            ->expects(self::any())
            ->method('__invoke')
            ->willReturn(
                $this->createInvocation($operation, $willRun, $willRollback)
            );

        $operation
            ->expects(self::any())
            ->method('__toString')
            ->willReturnCallback(
                fn () => $description
            );

        return $operation;
    }

    private function createStage(
        OperationInterface $operation,
        ?bool $willStage = true
    ): Stage {
        return new Stage(
            $operation,
            fn () => $willStage
        );
    }

    private function createInvocation(
        string|Stringable $description,
        bool $willRun = true,
        bool $willRollback = false
    ): Invocation {
        return new Invocation(
            $description,
            fn () => $willRun,
            fn () => $willRollback || throw new RuntimeException('Cannot roll back.')
        );
    }
}
