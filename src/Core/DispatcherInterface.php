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
