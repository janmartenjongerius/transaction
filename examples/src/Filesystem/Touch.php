<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Examples\Filesystem;

use DateTimeInterface;
use Johmanx10\Transaction\Operation\Operable;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;
use RuntimeException;

final class Touch implements OperationInterface
{
    use Operable;

    private bool $existed;
    private ?int $previousTime = null;

    public function __construct(
        private string $path,
        private ?DateTimeInterface $time
    ) {}

    protected function stageOperation(): ?bool
    {
        if ($this->time === null) {
            return StageResult::RESULT_STAGED;
        }

        if (!file_exists($this->path)) {
            return StageResult::RESULT_STAGED;
        }

        return filemtime($this->path) !== $this->time->getTimestamp()
            ? StageResult::RESULT_STAGED
            : StageResult::RESULT_SKIP;
    }

    protected function run(): ?bool
    {
        if ($this->existed = file_exists($this->path)) {
            $this->previousTime = filemtime($this->path);
        }

        return touch($this->path, $this->time?->getTimestamp());
    }

    protected function rollback(): void
    {
        if (!$this->existed) {
            if (is_dir($this->path)) {
                if (!rmdir($this->path)) {
                    throw new RuntimeException(
                        sprintf(
                            'Cannot remove directory "%s"',
                            $this->path
                        )
                    );
                }

                return;
            }

            if (!unlink($this->path)) {
                throw new RuntimeException(
                    sprintf(
                        'Cannot remove file "%s"',
                        $this->path
                    )
                );
            }

            return;
        }

        touch($this->path, $this->previousTime);
    }

    public function __toString(): string
    {
        return sprintf(
            'Touch: %s @%s',
            $this->path,
            date('r', $this->time?->getTimestamp() ?? time())
        );
    }

    public static function fromPath(
        string $path,
        ?DateTimeInterface $time = null
    ): iterable {
        yield from CreateDirectory::fromPath(dirname($path));
        yield new self($path, $time);
    }
}
