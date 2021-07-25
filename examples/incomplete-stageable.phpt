--TEST--
Stageable trait will not be implemented by class that does not implement operation interface.
--EXPECTF--
Fatal error: Uncaught %sException: Trait class@anonymous in /%s/src/Operation/Stageable.php:%d
Stack trace:
#0 /%s/examples/incomplete-stageable.php(%d): class@anonymous->stage()
#1 %s
#2 {main}
  thrown in /%s/src/Operation/Stageable.php on line %d
--FILE--
<?php

declare(strict_types=1);

use Johmanx10\Transaction\Operation\Stageable;
use Johmanx10\Transaction\Operation\Result\StageResult;

require_once __DIR__ . '/../vendor/autoload.php';

$operation = new class () {
    use Stageable;

    protected function stageOperation(): ?bool
    {
        return StageResult::RESULT_STAGED;
    }
};

$operation->stage();
