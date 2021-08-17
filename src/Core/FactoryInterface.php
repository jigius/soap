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
use Jigius\Soap\Core\Action\ActionInterface;
use Jigius\Soap\Core\WsdlData\WsdlDataInterface;

/**
 * Interface FactoryInterface
 *
 * @package Jigius\Soap\Core
 */
interface FactoryInterface
{
    /**
     * @return ActionInterface
     */
    public function action(): ActionInterface;

    /**
     * @return WsdlDataInterface
     */
    public function wsdlData(): WsdlDataInterface;

    /**
     * @param LogInterface $log
     * @return FactoryInterface
     */
    public function withLog(LogInterface $log): FactoryInterface;

    /**
     * @param string $name
     * @return FactoryInterface
     */
    public function withName(string $name): FactoryInterface;

    /**
     * @param ActionInterface $a
     * @return FactoryInterface
     */
    public function withAction(ActionInterface $a): FactoryInterface;

    /**
     * @param WsdlDataInterface $wsdlData
     * @return FactoryInterface
     */
    public function withWsdlData(WsdlDataInterface $wsdlData): FactoryInterface;
}
