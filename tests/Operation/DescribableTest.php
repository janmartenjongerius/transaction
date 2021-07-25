<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Tests\Descriptor;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Describable;
use Stringable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Describable
 */
class DescribableTest extends TestCase
{
    /**
     * @dataProvider descriptionProvider
     *
     * @covers ::__toString
     *
     * @param string|Stringable $description
     */
    public function testToString(string | Stringable $description): void
    {
        $subject = new class ($description) {
            use Describable;

            public function __construct(
                private string|Stringable $description
            ) {
            }
        };

        self::assertEquals((string)$description, (string)$subject);
    }

    /**
     * @return array<string,array<mixed>>
     */
    public function descriptionProvider(): array
    {
        return [
            'String' => [
                'description' => __CLASS__
            ],
            'Stringable' => [
                'description' => new Descriptor(__CLASS__)
            ]
        ];
    }
}
