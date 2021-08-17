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

final class FaultWithDetail implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $original;
    /**
     * @var string
     */
    private string $detail;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * FaultWithDetail constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param string $detail
     */
    public function __construct(Core\Task\TaskInterface $task, string $detail)
    {
        $this->original = $task;
        $this->executed = false;
        $this->detail = $detail;
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
        ob_start();
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
        echo preg_replace("~(</faultstring>)~", "\$1<detail>$this->detail</detail>", ob_get_clean());
        $obj = $this->blueprinted();
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
        $obj = new self($this->original, $this->detail);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
