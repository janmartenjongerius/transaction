<?php
declare(strict_types=1);

namespace Acme\Filesystem\Operation;

use Johmanx10\Transaction\Operation\Operable;
use Johmanx10\Transaction\Operation\OperationInterface;
use RuntimeException;

final class CreateDirectory implements OperationInterface
{
    use Operable;

    private bool $existed;

    public function __construct(private string $path, private int $mode) {}

    protected function stageOperation(): ?bool
    {
        return @is_dir($this->path) ? null : true;
    }

    protected function run(): ?bool
    {
        if ($this->existed = is_dir($this->path)) {
            return @chmod($this->path, $this->mode);
        }

        return @mkdir($this->path, $this->mode);
    }

    protected function rollback(): void
    {
        if (!$this->existed && is_dir($this->path) && !@rmdir($this->path)) {
            throw new RuntimeException(
                sprintf(
                    'Cannot remove directory "%s".',
                    $this->path
                )
            );
        }
    }

    /**
     * @param string $path
     * @param int    $mode
     *
     * @return OperationInterface[]
     */
    public static function fromPath(string $path, int $mode = 0755): iterable
    {
        $segments = explode(DIRECTORY_SEPARATOR, $path);
        $pointer = [];

        foreach (array_filter($segments) as $segment) {
            $pointer[] = $segment;
            yield new self(
                DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pointer),
                $mode
            );
        }
    }

    public function __toString(): string
    {
        return sprintf(
            'Create directory: %s with mode %o',
            $this->path,
            $this->mode
        );
    }
}
