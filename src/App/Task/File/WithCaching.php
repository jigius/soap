<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task\File;

use Jigius\Soap\App\Task\Result;
use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;

/**
 * Class WithCaching
 *
 * @package Jigius\Soap\App\Task\File
 */
final class WithCaching implements Core\Task\TaskInterface
{
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $valid;
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $invalid;
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var int
     */
    private int $ttl;
    /**
     * @var string
     */
    private string $pathfile;
    /**
     * @var bool
     */
    private bool $refreshed;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Cached constructor.
     *
     * @param Core\Task\TaskInterface $invalid
     * @param Core\Task\TaskInterface $valid
     * @param string $pathfile
     * @param int $ttl
     */
    public function __construct(
        Core\Task\TaskInterface $invalid,
        Core\Task\TaskInterface $valid,
        string $pathfile,
        int $ttl
    ) {
        $this->invalid = $invalid;
        $this->valid = $valid;
        $this->pathfile = $pathfile;
        $this->ttl = $ttl;
        $this->refreshed = false;
        $this->executed = false;
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        if ($this->executed) {
            throw new LogicException("task has being already executed");
        }
        $obj = $this->blueprinted();
        if ($this->expired()) {
            $excd =
                $this
                    ->invalid
                    ->withLog($this->log)
                    ->executed($r);
            $obj->invalid = $excd->task();
            $obj->refreshed = true;
        } else {
            $excd =
                $this
                    ->valid
                    ->withLog($this->log)
                    ->executed($r);
            $obj->valid = $excd->task();
        }
        $obj->log = $excd->log();
        $obj->refreshed = false;
        $obj->executed = true;
        return
            ($result ?? new Result())
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
     * @return bool
     */
    private function expired(): bool
    {
        if (!is_readable($this->pathfile)) {
            return true;
        }
        return time() - filemtime($this->pathfile) > $this->ttl;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->invalid, $this->valid, $this->pathfile, $this->ttl);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        $obj->refreshed = $this->refreshed;
        return $obj;
    }
}
