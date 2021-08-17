<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Task;

use Acc\Core\Log\LogInterface;

/**
 * Interface ResultInterface
 *
 * Implements an instance for the result of a task execution
 * @package Jigius\Soap\Core\Task
 */
interface EmbeddableResultInterface
{
    /**
     * @param TaskInterface $task
     * @return EmbeddableResultInterface
     */
    public function withTask(TaskInterface $task): EmbeddableResultInterface;

    /**
     * @param LogInterface $log
     * @return EmbeddableResultInterface
     */
    public function withLog(LogInterface $log): EmbeddableResultInterface;
}
