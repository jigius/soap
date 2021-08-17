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

namespace Jigius\Soap\App\Action;

use Acc\Core\Log;
use Jigius\Soap\Core;

/**
 * Class Factory
 *
 * @package Jigius\Soap\App\Action
 */
final class Factory implements Core\FactoryInterface
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var Core\Action\ActionInterface
     */
    private Core\Action\ActionInterface $action;
    /**
     * @var Core\WsdlData\WsdlDataInterface
     */
    private Core\WsdlData\WsdlDataInterface $wsdlData;

    /**
     * Factory constructor.
     */
    public function __construct()
    {
        $this->name = "unknown";
        $this->log = new Log\NullLog();
        $this->action = new Core\Action\DumbAction();
        $this->wsdlData = new Core\WsdlData\DumbWsdlData();
    }

    /**
     * @inheritDoc
     */
    public function action(): Core\Action\ActionInterface
    {
        return
            $this
                ->action
                ->withLog($this->log);
    }

    /**
     * @inheritDoc
     */
    public function wsdlData(): Core\WsdlData\WsdlDataInterface
    {
        return
            $this
                ->wsdlData
                ->withName($this->name)
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
     * @inheritDoc
     */
    public function withWsdlData(Core\WsdlData\WsdlDataInterface $wsdlData): self
    {
        $obj = $this->blueprinted();
        $obj->wsdlData = $wsdlData;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withName(string $name): self
    {
        $obj = $this->blueprinted();
        $obj->name = $name;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withAction(Core\Action\ActionInterface $a): self
    {
        $obj = $this->blueprinted();
        $obj->action = $a;
        return $obj;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->name = $this->name;
        $obj->log = $this->log;
        $obj->action = $this->action;
        $obj->wsdlData = $this->wsdlData;
        return $obj;
    }
}
