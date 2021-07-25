--TEST--
Logging subscribers at maximum verbosity.
--EXPECTF--
[info] [stage] Staged: Create directory: /%s/tests with mode 755
[info] [stage] Staged: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [stage] Staged: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [stage] Staged: Copy file /%s/examples/logging-subscriber.phpt to /%s/tests/logging-subscriber.phpt.out
[info] [stage] Staged: Create directory: /%s/tests with mode 755
[info] [stage] Staged: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [stage] Staged: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [stage] Staged: Copy file /%s/vendor/autoload.php to /%s/tests/logging-subscriber.phpt.out
[info] Transaction staged
[info] [invoke] Create directory: /%s/tests with mode 755
[info] [invoke] Success: Create directory: /%s/tests with mode 755
[info] [invoke] Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [invoke] Success: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [invoke] Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [invoke] Success: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [invoke] Copy file /%s/examples/logging-subscriber.phpt to /%s/tests/logging-subscriber.phpt.out
[info] [invoke] Success: Copy file /%s/examples/logging-subscriber.phpt to /%s/tests/logging-subscriber.phpt.out
[info] [invoke] Create directory: /%s/tests with mode 755
[info] [invoke] Success: Create directory: /%s/tests with mode 755
[info] [invoke] Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [invoke] Success: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[info] [invoke] Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [invoke] Success: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[info] [invoke] Copy file /%s/vendor/autoload.php to /%s/tests/logging-subscriber.phpt.out
[error] Cannot override existing file: "/%s/logging-subscriber.phpt.out/index.php"
[error] [invoke] Failed: Copy file /%s/vendor/autoload.php to /%s/tests/logging-subscriber.phpt.out
[error] Cannot override existing file: "/%s/logging-subscriber.phpt.out/index.php"
[error] Transaction not committed
[warning] [rollback] Rolling back: Copy file /%s/vendor/autoload.php to /%s/tests/logging-subscriber.phpt.out
[warning] [rollback] Rolling back: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[warning] [rollback] Rolling back: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[warning] [rollback] Rolling back: Create directory: /%s/tests with mode 755
[warning] [rollback] Rolling back: Copy file /%s/examples/logging-subscriber.phpt to /%s/tests/logging-subscriber.phpt.out
[warning] [rollback] Rolling back: Touch: /%s/tests/logging-subscriber.phpt.out/index.php @%s, %d %s %d %d:%d:%d %s
[warning] [rollback] Rolling back: Create directory: /%s/tests/logging-subscriber.phpt.out with mode 755
[warning] [rollback] Rolling back: Create directory: /%s/tests with mode 755
[info] Performed 8 rollback(s)
--FILE--
<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Acme\Filesystem\Operation\CopyFile;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Johmanx10\Transaction\Operation\OperationHandler;
use Johmanx10\Transaction\TransactionFactory;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$dispatcher = new EventDispatcher();
$factory = new TransactionFactory($dispatcher);
$handler = new OperationHandler($factory);
$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_VERY_VERBOSE);
$logger = new ConsoleLogger($output);

$dispatcher->addSubscriber(
    new TransactionLoggerSubscriber($logger)
);
$dispatcher->addSubscriber(
    new OperationLoggerSubscriber($logger)
);

$destination = sys_get_temp_dir() . '/tests/' . basename(__FILE__) . '.out/index.php';
$result = $handler(
    CopyFile::fromPath(
        source: __FILE__,
        destination: $destination,
        overrideExisting: CopyFile::OVERRIDE_EXISTING_FILE
    ),
    CopyFile::fromPath(
        source: __DIR__ . '/../vendor/autoload.php',
        destination: $destination,
        overrideExisting: CopyFile::PRESERVE_EXISTING_FILE
    )
);
