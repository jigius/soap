<?php
namespace Jigius\Soap\App\Action\Ping;

use Jigius\Soap\App\Action\Ping\Response\Successed;
use Jigius\Soap\App\Action\Ping\Response\Vanilla;
use Jigius\Soap\Core;
use Jigius\Soap\App\Action\Result;
use Acc\Core\Log;
use LogicException;

/**
 * Class Action
 *
 * @package Jigius\Soap\App\Action\Ping
 */
final class Action implements Core\Action\ActionInterface
{
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * Action constructor.
     */
    public function __construct()
    {
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function executed(array $args, ?Core\Action\EmbeddableResultInterface $r = null): Core\Action\ResultInterface
    {
        $payload = array_shift($args) ?? "";
        $ret =
            (new Successed(
                new Vanilla()
            ))
                ->with("payload", $payload)
                ->finished();
        if (!is_array($ret)) {
            throw new LogicException("type invalid");
        }
        return
            ($r ?? new Result())
                ->withResponse(
                    (object)$ret
                )
                ->withLog($this->log);
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
        return $obj;
    }
}
