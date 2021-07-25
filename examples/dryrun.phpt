--TEST--
Dry-run transaction.
--EXPECT--
bool(true)
bool(true)
bool(false)
--FILE--
<?php

declare(strict_types=1);

use Acme\Filesystem\Operation\CopyFile;
use Johmanx10\Transaction\DryRun;
use Johmanx10\Transaction\TransactionFactory;

$factory = new TransactionFactory(
    strategy: TransactionFactory::STRATEGY_DRY_RUN
);

$destination = sys_get_temp_dir() . '/' . __FILE__ . '.out/composer.json';
$transaction = $factory(
    ...CopyFile::fromPath(
        __DIR__ . '/../composer.json',
        $destination,
        overrideExisting: CopyFile::OVERRIDE_EXISTING_FILE
    )
);

var_dump($transaction instanceof DryRun);

$result = $transaction->commit();

var_dump($result->committed());
var_dump(file_exists($destination));
