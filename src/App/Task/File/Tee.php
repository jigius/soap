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

final class Tee implements Core\Task\TaskInterface
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
    private string $pathfile;
    /**
     * @var string
     */
    private string $flags;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Tee constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param string $pathfile
     * @param string $flags
     */
    public function __construct(Core\Task\TaskInterface $task, string $pathfile, string $flags = "wb+")
    {
        $this->original = $task;
        $this->pathfile = $pathfile;
        $this->flags = $flags;
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
        ob_start();
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
        $output = ob_get_clean();
        echo $output;
        $this->write($output);
        $obj = $this->blueprinted();
        $obj->log = $excd->log();
        $obj->original = $excd->task();
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
     * @param string $bytes
     */
    private function write(string $bytes): void
    {
        $fd = @fopen($this->pathfile, $this->flags);
        if ($fd === false) {
            throw new RuntimeException("Couldn't open the file=`{$this->pathfile}` with flags=`{$this->flags}`");
        }
        if (@fwrite($fd, $bytes) === false) {
            throw new RuntimeException("Couldn't write to the file=`{$this->pathfile}`");
        }
        @fclose($fd);
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->original, $this->pathfile, $this->flags);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
