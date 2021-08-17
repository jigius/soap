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

namespace Jigius\Soap\App\Task;

use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;

/**
 * Class Nop
 *
 * Does nothing!
 *
 * @package Jigius\Soap\App\Task
 */
final class Nop implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * NoOp constructor.
     */
    public function __construct()
    {
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
        $obj = new self();
        $obj->executed = true;
        return
            ($r ?? new Result())
                ->withLog($this->log)
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
        $obj = new self();
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
