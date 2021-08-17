<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Task;

use Acc\Core\Log\LogInterface;

/**
 * Interface ResultInterface
 *
 * @package Jigius\Soap\Core\Task
 */
interface ResultInterface
{
    /**
     * @return LogInterface
     */
    public function log(): LogInterface;

    /**
     * @return TaskInterface
     */
    public function task(): TaskInterface;
}
