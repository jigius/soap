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

use Jigius\Soap\App\Action;
use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;
use Jigius\Soap\Core\DispatcherInterface;
use SoapServer;

/**
 * Class Operation
 *
 * @package Jigius\Soap\App\Task
 */
final class Operation implements Core\Task\TaskInterface
{
    private DispatcherInterface $dsptchr;
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var SoapServer
     */
    private SoapServer $server;

    /**
     * Operation constructor.
     *
     * @param SoapServer $s
     * @param DispatcherInterface $d
     */
    public function __construct(SoapServer $s, DispatcherInterface $d)
    {
        $this->server = $s;
        $this->dsptchr = $d;
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
        $d =
            $this
                ->dsptchr
                ->withLog($this->log)
                ->withResult($r ?? new Action\Result());
        $this->server->setObject($d);
        $this->server->handle();
        $obj = $this->blueprinted();
        $obj->log = $d->result()->log();
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
        $obj = new self($this->server, $this->dsptchr);
        $obj->log = $this->log;
        return $obj;
    }
}
