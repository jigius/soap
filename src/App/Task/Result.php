<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task;

use Acc\Core\Log;
use Jigius\Soap\Core;
use Jigius\Soap\Core\Task\TaskInterface;

/**
 * Class Result
 *
 * @package App\Soap\Task
 */
final class Result implements Core\Task\ResultInterface, Core\Task\EmbeddableResultInterface
{
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $task;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->task = new Core\Task\DumbTask();
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     */
    public function withTask(Core\Task\TaskInterface $task): self
    {
        $obj = $this->blueprinted();
        $obj->task = $task;
        return $obj;
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
     * @inheritDoc
     */
    public function log(): Log\LogInterface
    {
        return $this->log;
    }

    /**
     * @inheritDoc
     */
    public function task(): TaskInterface
    {
        return $this->task;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->log = $this->log;
        $obj->task = $this->task;
        return $obj;
    }
}
