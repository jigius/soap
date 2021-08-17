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

namespace Jigius\Soap\App;

use Jigius\Soap\App\Action\ActionIsNotDefinedException;
use Jigius\Soap\App\Action\Result;
use Jigius\Soap\Core\Action\EmbeddableResultInterface;
use Jigius\Soap\Core\Action\ResultInterface;
use Jigius\Soap\Core\FactoryInterface;
use Acc\Core\Registry\RegistryInterface;
use Acc\Core\Value\Vanilla\Asset\HasContract;
use Acc\Core\Value\Vanilla\FailedException;
use Acc\Core\Value\Vanilla\WithConstraint;
use Jigius\Soap\Core\DispatcherInterface;
use Acc\Core\Log;
use LogicException;

/**
 * Class Dispatcher
 *
 * @package Jigius\Soap\App
 */
final class Dispatcher implements DispatcherInterface
{
    /**
     * @var RegistryInterface
     */
    private RegistryInterface $operation;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var ResultInterface|null
     */
    private ?ResultInterface $ro;
    /**
     * @var EmbeddableResultInterface
     */
    private EmbeddableResultInterface $ri;

    /**
     * DispatcherOfOperation constructor.
     *
     * @param RegistryInterface $operation
     */
    public function __construct(RegistryInterface $operation)
    {
        $this->operation = $operation;
        $this->log = new Log\NullLog();
        $this->ri = new Result();
        $this->ro = null;
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
    public function __call(string $method, array $args): object
    {
        try {
            $factory =
                (new WithConstraint(
                    $this->operation->pulled($method)
                ))
                    ->withAsset(
                        new HasContract(FactoryInterface::class)
                    )
                    ->fetch();
        } catch (FailedException $ex) {
            throw
                new LogicException(
                    "Couldn't get a factory instance is specified for `{$method}` action",
                    0,
                    $ex
                );
        }
        $result =
            $factory
                ->withName($method)
                ->action()
                ->withLog($this->log)
                ->executed($args, $this->ri);
        $response = $result->response();
        if (!isset($response) || !is_object($response)) {
            throw new LogicException("type invalid");
        }
        $this->ro = $result;
        return $response;
    }

    /**
     * @inheritDoc
     * @throws ActionIsNotDefinedException
     */
    public function result(): ResultInterface
    {
        if ($this->ro === null) {
            throw new ActionIsNotDefinedException("No action is executed :/");
        }
        return $this->ro;
    }

    /**
     * @inheritDoc
     */
    public function withResult(EmbeddableResultInterface $r): self
    {
        $obj = $this->blueprinted();
        $obj->ri = $r;
        return $obj;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->operation);
        $obj->log = $this->log;
        $obj->ri = $this->ri;
        $obj->ro = $this->ro;
        return $obj;
    }
}
