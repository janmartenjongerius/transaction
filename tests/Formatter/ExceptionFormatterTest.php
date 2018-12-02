<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests\Formatter;

use Exception;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Formatter\ExceptionFormatter;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Formatter\ExceptionFormatter
 */
class ExceptionFormatterTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::format
     */
    public function testFormat(): void
    {
        $subject = new ExceptionFormatter();

        $this->assertEquals(
            'Foo',
            $subject->format(new Exception('Foo'))
        );
    }
}
