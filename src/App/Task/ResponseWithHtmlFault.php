<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task;

use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;

/**
 * Class ResponseWithHtmlFault
 *
 * @package Jigus\Soap\App\Task
 */
final class ResponseWithHtmlFault implements Core\Task\TaskInterface
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
     * @var string
     */
    private string $content;
    /**
     * @var int
     */
    private int $code;

    /**
     * ResponseWithFault constructor.
     *
     * @param string $content
     * @param int $code
     */
    public function __construct(string $content, int $code = 200)
    {
        $this->log = new Log\NullLog();
        $this->content = $content;
        $this->code = $code;
        $this->executed = false;
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        if ($this->executed) {
            throw new LogicException("task has being already executed");
        }
        echo $this->content;
        http_response_code($this->code);
        $obj = $this->blueprinted();
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
        $obj = new self($this->content, $this->code);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
