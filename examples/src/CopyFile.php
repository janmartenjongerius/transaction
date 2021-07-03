<?php
declare(strict_types=1);

namespace Acme\Filesystem\Operation;

use Johmanx10\Transaction\Operation\Operable;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;
use SplFileInfo;
use SplFileObject;
use SplTempFileObject;

final class CopyFile implements OperationInterface
{
    use Operable;

    public const OVERRIDE_EXISTING_FILE = true;
    public const PRESERVE_EXISTING_FILE = false;

    private ?SplFileObject $buffer = null;

    public function __construct(
        private SplFileInfo $source,
        private SplFileInfo $destination,
        private bool $overrideExisting
    ) {}

    protected function stageOperation(): ?bool
    {
        if (!$this->source->isFile()) {
            return StageResult::RESULT_FAILED;
        }

        if ($this->destination->isFile()) {
            if ($this->overrideExisting === false) {
                return StageResult::RESULT_FAILED;
            }

            if ($this->source->getMTime() > $this->destination->getMTime()) {
                return StageResult::RESULT_STAGED;
            }

            if ($this->source->getSize() != $this->destination->getSize()) {
                return StageResult::RESULT_STAGED;
            }

            return StageResult::RESULT_SKIP;
        }

        return StageResult::RESULT_STAGED;
    }

    private static function stream(
        SplFileObject $source,
        SplFileObject $destination
    ): int {
        $source->rewind();

        $numWritten = 0;

        foreach ($source as $line) {
            $numWritten += $destination->fwrite($line);
        }

        return $numWritten;
    }

    protected function run(): ?bool
    {
        if ($this->overrideExisting === false
            && $this->destination->getRealPath() !== false
        ) {
            return false;
        }

        if ($this->destination->getSize() > 0) {
            $this->buffer = new SplTempFileObject();

            if (
                self::stream(
                    $this->destination->openFile(),
                    $this->buffer
                ) !== $this->destination->getSize()
            ) {
                return false;
            }
        }

        return self::stream(
            $this->source->openFile(),
            $this->destination->openFile('w')
        ) === $this->source->getSize();
    }

    protected function rollback(): void
    {
        if ($this->buffer) {
            self::stream($this->buffer, $this->destination->openFile('w'));
        }
    }

    public function __toString(): string
    {
        return sprintf(
            'Copy file %s to %s',
            $this->source->getRealPath(),
            $this->destination->getPath()
        );
    }

    /**
     * @param string $source
     * @param string $destination
     * @param bool   $overrideExisting
     *
     * @return OperationInterface[]
     */
    public static function fromPath(
        string $source,
        string $destination,
        bool $overrideExisting = self::PRESERVE_EXISTING_FILE
    ): iterable {
        yield from Touch::fromPath($destination);
        yield new self(
            new SplFileInfo(realpath($source) ?: $source),
            new SplFileInfo($destination),
            $overrideExisting
        );
    }
}
