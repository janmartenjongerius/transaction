<?php


namespace Johmanx10\Transaction\Tests\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Exception\TransactionRolledBackException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Exception\TransactionRolledBackException
 */
class TransactionRolledBackExceptionTest extends TestCase
{
    /**
     * @return array
     */
    public function failureProvider(): array
    {
        return [
            [],
            [
                $this->createMock(OperationFailureInterface::class)
            ],
            [
                $this->createMock(OperationFailureInterface::class),
                $this->createMock(OperationFailureInterface::class),
                $this->createMock(OperationFailureInterface::class)
            ]
        ];
    }

    /**
     * @dataProvider failureProvider
     *
     * @param OperationFailureInterface ...$failures
     *
     * @return void
     *
     * @covers ::__construct
     * @covers ::getFailures
     */
    public function testException(OperationFailureInterface ...$failures): void
    {
        $subject = new TransactionRolledBackException(...$failures);

        $this->assertInstanceOf(
            TransactionRolledBackException::class,
            $subject
        );

        $this->assertEquals($failures, $subject->getFailures());
        $this->assertStringMatchesFormat(
            sprintf(
                '%d operations were rolled back: %s',
                count($failures),
                implode(
                    ', ',
                    array_fill(0, count($failures), '%d')
                )
            ),
            $subject->getMessage()
        );
    }
}
