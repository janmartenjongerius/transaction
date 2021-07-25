--TEST--
Ensure that the operation handler can restore the default rollback mechanism.
--EXPECT--
bool(true)
bool(false)
bool(true)
--FILE--
<?php

declare(strict_types=1);

use Johmanx10\Transaction\TransactionFactory;
use Johmanx10\Transaction\Operation\OperationHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$handler = new OperationHandler(new TransactionFactory());
$property = new ReflectionProperty(OperationHandler::class, 'rollback');

$mutated = $handler->withRollback(fn () => null);
$restored = $mutated->defaultRollback();

$property->setAccessible(true);

var_dump(is_null($property->getValue($handler)));
var_dump(is_null($property->getValue($mutated)));
var_dump(is_null($property->getValue($restored)));
