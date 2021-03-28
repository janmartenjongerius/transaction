<?php


namespace Johmanx10\Transaction\Tests\Formatter;

use Johmanx10\Transaction\DescribableOperationInterface;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Formatter\OperationFormatter;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Formatter\OperationFormatter
 */
class OperationFormatterTest extends TestCase
{
    /**
     * @param string $description
     *
     * @return DescribableOperationInterface
     */
    private function createDescribableOperation(
        string $description
    ): DescribableOperationInterface {
        /** @var DescribableOperationInterface|MockObject $operation */
        $operation = $this->createMock(DescribableOperationInterface::class);

        $operation
            ->expects(self::any())
            ->method('__toString')
            ->willReturn($description);

        return $operation;
    }

    /**
     * @return array
     */
    public function operationProvider(): array
    {
        $operation = $this->createMock(OperationInterface::class);

        return [
            [
                $operation,
                sprintf('Generic operation %s', spl_object_hash($operation))
            ],
            [
                $this->createDescribableOperation('Foo is my name'),
                'Foo is my name'
            ]
        ];
    }

    /**
     * @dataProvider operationProvider
     *
     * @param OperationInterface $operation
     * @param string             $expected
     *
     * @return void
     *
     * @covers ::format
     */
    public function testFormat(
        OperationInterface $operation,
        string $expected
    ): void {
        $subject = new OperationFormatter();
        $this->assertEquals($expected, $subject->format($operation));
    }
}
