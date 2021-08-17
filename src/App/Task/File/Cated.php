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

final class Cated implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var string
     */
    private string $pathfile;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Cat constructor.
     *
     * @param string $pathfile
     */
    public function __construct(string $pathfile)
    {
        $this->pathfile = $pathfile;
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
        if (($fd = @fopen($this->pathfile, "rb")) === false) {
            throw new RuntimeException("Couldn't open the file=`$this->pathfile`");
        }
        if (($output = @fpassthru($fd)) === false) {
            throw new RuntimeException("Couldn't read data from the file=`$this->pathfile`");
        }
        @fclose($fd);
        $obj = $this->blueprinted();
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
        $obj = new self($this->pathfile);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
