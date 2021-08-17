<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task\Http;

use Jigius\Soap\App\Task\Result;
use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;

/**
 * Class AddedHeaders
 *
 * @package Jigius\Soap\App\Task\Http
 */
final class AddedHeaders implements Core\Task\TaskInterface
{
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $original;
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var array
     */
    private array $h;
    /**
     * @var bool
     */
    private bool $replace;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * AddedHeaders constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param array $headers
     * @param bool $replace
     */
    public function __construct(Core\Task\TaskInterface $task, array $headers, bool $replace = false)
    {
        $this->original = $task;
        $this->h = $headers;
        $this->replace = $replace;
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
        foreach ($this->h as $h) {
            header($h, $this->replace);
        }
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
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
        $obj = new self($this->original, $this->h, $this->replace);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
