--TEST--
Rollbacks must not use operations that aren't invoked.
--EXPECT--
Invoked
Rolling back #2
Rolling back #1
Finished
--FILE--
<?php

declare(strict_types=1);

use Johmanx10\Transaction\Operation\Operation;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\Transaction;

$transaction = new Transaction(
    new Operation(
        description: 'Successful operation',
        invocation: fn () => print 'Invoked' . PHP_EOL,
        rollback: fn () => print 'Rolling back #1' . PHP_EOL
    ),
    new Operation(
        description: 'Skipped operation',
        invocation: fn () => throw new RuntimeException('Must not run'),
        rollback: fn () => throw new RuntimeException('Must not roll back'),
        stage: fn () => StageResult::RESULT_SKIP
    ),
    new Operation(
        description: 'Broken operation',
        invocation: fn () => throw new RuntimeException('Break'),
        rollback: fn () => print 'Rolling back #2' . PHP_EOL
    ),
    new Operation(
        description: 'Never invoked',
        invocation: fn () => throw new RuntimeException('Must not run'),
        rollback: fn () => throw new RuntimeException('Must not roll back')
    )
);

$result = $transaction->commit();
$result->rollback();

echo 'Finished' . PHP_EOL;
