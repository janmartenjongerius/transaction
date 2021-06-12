# Operation handler

The operation handler is an abstraction around transactions that simplifies its
API in a service oriented architecture.

Instead of manually creating concrete transaction instances, the operation
handler defers this to an internal transaction factory.

It also provides the following:

- [Mixed operation types](#mixed-operation-types)
- [Custom rollbacks](#custom-rollback)

# Mixed operation types

Given the support for mixed types in PHP 8, the handler now accepts the variadic
argument `OperationInterface | iterable ...$operations`. Effectively, this boils
down the the type `(OperationInterface | OperationInterface[]) ...$operations`,
which is then internally flattened to `OperationInterface ...$operations`.

This allows for more expressive mixing of arguments, to mix arrays, iterators
and singular operation instances in the same call, without having to explicitly
expand the iterable arguments.

Consider the following operation:

```php
<?php
namespace Acme\Filesystem\Operation;

use Johmanx10\Transaction\Operation\OperationInterface;
use SplFileInfo;

class CopyFile implements OperationInterface
{
    // ... Implementation of operation
    
    public static function fromPath(
        string $source,
        string $destination,
        bool $overrideExisting = false
    ): iterable {
        // This ensures the directory exists and the file can be created.
        yield from Touch::fromPath($destination);
        yield new self(
            new SplFileInfo(realpath($source)),
            new SplFileInfo($destination),
            $overrideExisting
        );
    }
}
```

This allows the handler to be invoked as such:

```php
<?php

use Johmanx10\Transaction\Operation\OperationHandlerInterface;
use Acme\Filesystem\Operation\CopyFile;

[, $destination] = $argv;

/** @var OperationHandlerInterface $handler */
$handler(
    CopyFile::fromPath('dist/icon.ico', $destination . '/icon.ico'),
    CopyFile::fromPath('dist/dynamite', $destination . '/dynamite'),
    CopyFile::fromPath('dist/tnt', $destination . '/tnt')
);
```

The handler above seemingly invokes 3 operations, although each line may
represent multiple operations, depending on the directory depth of `$destination`
and whether directories need to be created, recursively.

```php
<?php

use Johmanx10\Transaction\Operation\OperationHandlerInterface;
use Acme\Filesystem\Operation\CopyFile;
use Acme\Filesystem\Operation\Gunzip;

[, $destination] = $argv;

/** @var OperationHandlerInterface $handler */
$handler(
    CopyFile::fromPath('dist/icon.ico.gz', $destination . '/icon.ico.gz'),
    new Gunzip('dist/icon.ico.gz'),
    CopyFile::fromPath('dist/dynamite', $destination . '/dynamite'),
    CopyFile::fromPath('dist/tnt', $destination . '/tnt')
);
```

The above successfully mixes single operations with iterable of operations.

Note that the above has room for performance improvements. Because it first
copies over the archive and then extracts it, instead of extracting the archive
directly to the right destination and streaming the content.

Consider the following to solve that problem seamlessly:

```php
<?php

use Johmanx10\Transaction\Operation\OperationHandlerInterface;
use Acme\Filesystem\Operation\CopyFile;
use Acme\Filesystem\Operation\Gunzip;

[, $destination] = $argv;

/** @var OperationHandlerInterface $handler */
$handler(
    Gunzip::fromPath('dist/icon.ico.gz', $destination, 'icon.ico'),
    CopyFile::fromPath('dist/dynamite', $destination . '/dynamite'),
    CopyFile::fromPath('dist/tnt', $destination . '/tnt')
);
```

This again looks like a single operation, but conceals multiple operations under
the hood, making each individual operation more compact, purpose-built and
reusable.

Given that `$destination` is set to `/home/acme/bin`, the following shows a possible
list of operations that come from the single `Gunzip::fromPath` call:

```yaml
- operation: CreateDirectory
  arguments:
    directory: /home
    mode: 0755
- operation: CreateDirectory
  arguments:
    directory: /home/acme
    mode: 0755
- operation: CreateDirectory
  arguments:
    directory: /home/acme/bin
    mode: 0755
- operation: Touch
  arguments:
    path: /home/acme/bin/icon.ico
    time: ~
- operation: Gunzip
  arguments:
    archive: dist/icon.ico.gz
    destination: /home/acme/bin
    files:
      icon.ico: icon.ico
```

# Custom rollback

To provide a custom rollback for the whole transaction, the following methods
are available on the operation handler:

| Method signature                            | Description |
|:--------------------------------------------|:------------|
| `withRollback(callable $rollback): static;` | Perform the following transactions with the given rollback, when required. |
| `defaultRollback(): static;`                | Resets the custom rollback and performs following transactions with the default rollback behaviour. |

```php
use Acme\Filesystem\Operation\CopyFile;
use Acme\Filesystem\Operation\MoveFile;
use Johmanx10\Transaction\Operation\OperationHandlerInterface;

$partition = '/dev/sda1';
$image = getcwd() . '/sda1.img';
$partial = $image . '.part';

/** @var OperationHandlerInterface $handler */
$handler->withRollback(fn () => @unlink($partial))(
    CopyFile::fromPath($partition, $partial),
    MoveFile::fromPath($partial, $image)
);
```
