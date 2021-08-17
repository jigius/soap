<?php
declare(strict_types=1);

namespace Jigius\Soap\Core;

use Acc\Core\Log\LogInterface;
use Jigius\Soap\Core\Action\EmbeddableResultInterface;
use Jigius\Soap\Core\Action\ResultInterface;

/**
 * Interface DispatcherOfOperationsInterface
 *
 * @package Jigius\Soap\Core
 */
interface DispatcherInterface extends EmbeddableLogInterface
{
    /**
     * Handles a request from SOAP-server. Does a mutation of the instance's state!
     *
     * @param string $method
     * @param array $args
     * @return object
     */
    public function __call(string $method, array $args): object;

    /**
     * Returns the result of a request(from SOAPServer) execution
     * @return ResultInterface
     */
    public function result(): ResultInterface;

    /**
     * @param EmbeddableResultInterface $r
     * @return DispatcherInterface
     */
    public function withResult(EmbeddableResultInterface $r): DispatcherInterface;

    /**
     * @inheritDoc
     * @return DispatcherInterface
     */
    public function withLog(LogInterface $log): DispatcherInterface;
}
