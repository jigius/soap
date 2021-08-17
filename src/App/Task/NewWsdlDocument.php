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

use Acc\Core\Registry\RegistryInterface;
use Acc\Core\Value\Vanilla\Asset\HasContract;
use Acc\Core\Value\Vanilla\FailedException;
use Acc\Core\Value\Vanilla\WithConstraint;
use Jigius\Soap\Core\FactoryInterface;
use Jigius\Soap\Core\Printer\WsdlDocument;
use LogicException;
use Acc\Core\Log;
use Jigius\Soap\Core;

/**
 * Class NewWsdlDocument
 *
 * @package Jigius\Soap\App\Task
 */
final class NewWsdlDocument implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var string
     */
    private string $serverUrlPrefix;
    /**
     * @var string
     */
    private string $targetNS;
    /**
     * @var RegistryInterface
     */
    private RegistryInterface $operation;
    /**
     * @var array
     */
    private array $opts;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * NewWsdlDocument constructor.
     *
     * @param string $serverUrlPrefix
     * @param string $targetNS
     * @param RegistryInterface $operation
     * @param array $opts
     */
    public function __construct(
        string $serverUrlPrefix,
        string $targetNS,
        RegistryInterface $operation,
        array $opts = []
    ) {
        $this->serverUrlPrefix = $serverUrlPrefix;
        $this->targetNS = $targetNS;
        $this->operation = $operation;
        $this->opts = $opts;
        $this->executed = false;
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        $printer = new WsdlDocument(
            $this->serverUrlPrefix,
            $this->targetNS,
            $this->opts
        );
        $log = $this->log;
        foreach ($this->operation->iterator() as $name => $factory) {
            try {
                $factory =
                    (new WithConstraint($factory))
                        ->withAsset(
                            new HasContract(FactoryInterface::class)
                        )
                        ->fetch();
            } catch (FailedException $ex) {
                throw
                    new LogicException(
                        "Couldn't get a factory instance is specified for `{$name}` action",
                        0,
                        $ex
                    );
            }
            $excd =
                $factory
                    ->withName($name)
                        ->withLog($log)
                        ->wsdlData()
                        ->executed($printer);
            $printer = $excd->printer();
            $log = $excd->log();
        }
        $printer->finished();
        $obj = $this->blueprinted();
        $obj->log = $log;
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
        $obj = new self($this->serverUrlPrefix, $this->targetNS, $this->operation, $this->opts);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
