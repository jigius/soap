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

namespace Jigius\Soap\App\Task\File;

use Jigius\Soap\App\Task\Result;
use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;
use RuntimeException;

/**
 * Class WithRenamedFile
 *
 * @package Jigius\Soap\App\Task\File
 */
final class WithRenamedFile implements Core\Task\TaskInterface
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
     * @var string
     */
    private string $from;
    /**
     * @var string
     */
    private string $to;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Renamed constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param string $from
     * @param string $to
     */
    public function __construct(Core\Task\TaskInterface $task, string $from, string $to)
    {
        $this->original = $task;
        $this->from = $from;
        $this->to = $to;
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
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
        $this->rename($this->from, $this->to);
        $obj = $this->blueprinted();
        $obj->original = $excd->task();
        $obj->executed = true;
        return
            ($r ?? new Result())
                ->withLog($excd->log())
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
     * @param string $from
     * @param string $to
     */
    private function rename(string $from, string $to)
    {
        $ret = @rename($from, $to);
        if ($ret === false) {
            throw new RuntimeException("could not rename the file=`$from` to=`$to`");
        }
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->original, $this->from, $this->to);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
