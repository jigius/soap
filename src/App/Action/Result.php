<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Action;

use Acc\Core\Log;
use Jigius\Soap\Core\Action;

/**
 * Class Result
 *
 * @package Jigius\Soap\App\Action
 */
final class Result implements Action\ResultInterface, Action\EmbeddableResultInterface
{
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var object|null
     */
    private ?object $response;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->log = new Log\NullLog();
        $this->response = null;
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
     * @inheritDoc
     */
    public function log(): Log\LogInterface
    {
        return $this->log;
    }

    /**
     * @inheritDoc
     */
    public function response(): ?object
    {
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function withResponse(object $val): self
    {
        $obj = $this->blueprinted();
        $obj->response = $val;
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
        $obj->response = $this->response;
        return $obj;
    }
}
