<?php
/**
 * This file is part of the jigius/soap library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2021 Jigius <jigius@gmail.com>
 * @link https://github.com/jigius/soap GitHub
 */

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
