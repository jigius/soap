<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task\File;

use Jigius\Soap\App\Task\Result;
use Jigius\Soap\Core;
use Acc\Core\Log;
use RuntimeException;

/**
 * Class WithLockedFile
 *
 * @package Jigius\Soap\App\Task\File
 */
final class WithLockedFile implements Core\Task\TaskInterface
{
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $original;
    /**
     * @var LockedFileInterface
     */
    private LockedFileInterface $lock;
    /**
     * @var int|mixed
     */
    private int $flags;
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Locked constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param LockedFileInterface $lock
     * @param int $flags
     */
    public function __construct(
        Core\Task\TaskInterface $task,
        LockedFileInterface $lock,
        int $flags = LOCK_EX
    ) {
        $this->original = $task;
        $this->flags = $flags;
        $this->lock = $lock;
        $this->executed = false;
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        $lock = $this->lock->acquired($this->flags);
        if (!$lock->locked()) {
            throw new RuntimeException("Couldn't get a lock!");
        }
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
        $lock->released();
        $obj = $this->blueprinted();
        $obj->original = $excd->task();
        $obj->log = $excd->log();
        $obj->executed = true;
        return
            ($r ?? new Result())
                ->withLog($obj->log)
                ->withTask($obj);
    }

    /**
     * @inheritDoc
     */
    public function withLog(Log\LogInterface $log): self
    {
        $obj = $this->blueprinted();
        $obj->log = $log;
        return $obj;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->original, $this->lock, $this->flags);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
