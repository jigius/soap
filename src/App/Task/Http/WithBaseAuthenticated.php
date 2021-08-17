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

namespace Jigius\Soap\App\Task\Http;

use LogicException;
use InvalidArgumentException;
use Acc\Core\Log;
use Jigius\Soap\App\Task\Result;
use Jigius\Soap\Core;

/**
 * Class WithBaseAuthenticated
 *
 * @package Jigius\Soap\App\Task\Http
 */
final class WithBaseAuthenticated implements Core\Task\TaskInterface
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
    private string $authData;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * WithBaseAuthenticated constructor.
     *
     * @param Core\Task\TaskInterface $task
     * @param string $authData
     */
    public function __construct(Core\Task\TaskInterface $task, string $authData = "")
    {
        $this->original = $task;
        $this->authData = $authData;
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
        if (!empty($this->authData)) {
            if (!isset($_SERVER['HTTP_AUTHORIZATION']) ||
                !preg_match("/^Basic\s+(.*)$/", $_SERVER['HTTP_AUTHORIZATION'], $m) ||
                $this->authData !== $m[1]
            ) {
                throw new InvalidArgumentException("Forbidden", 403);
            }
        }
        $excd =
            $this
                ->original
                ->withLog($this->log)
                ->executed($r);
        $obj = $this->blueprinted();
        $obj->original = $excd->task();
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
        $obj = new self($this->original, $this->authData);
        $obj->log = $this->log;
        return $obj;
    }
}
