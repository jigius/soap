<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Task;

use Acc\Core\Log\LogInterface;
use Jigius\Soap\Core\EmbeddableLogInterface;

/**
 * Interface TaskInterface
 *
 * @package Jigius\Soap\Core\Task
 */
interface TaskInterface extends EmbeddableLogInterface
{
    /**
     * @param EmbeddableResultInterface|null $r
     * @return ResultInterface
     */
   public function executed(?EmbeddableResultInterface $r = null): ResultInterface;

    /**
     * @inheritDoc
     * @return TaskInterface
     */
   public function withLog(LogInterface $log): TaskInterface;
}
