<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Task;

use Acc\Core\Log\LogInterface;
use LogicException;

/**
 * Class DumbTask
 *
 * @package Jigius\Soap\Core\Task
 */
final class DumbTask implements TaskInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function withLog(LogInterface $log): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function executed(?EmbeddableResultInterface $r = null): ResultInterface
    {
        throw new LogicException("Just a stub");
    }
}
