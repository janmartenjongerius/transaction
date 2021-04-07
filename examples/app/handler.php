<?php

declare(strict_types=1);

use Johmanx10\Transaction\Operation\OperationHandler;

[$input, $output] = require __DIR__ . '/console.php';

return new OperationHandler(
    (require __DIR__ . '/factory.php')($input, $output)
);
