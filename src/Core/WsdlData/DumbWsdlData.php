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

namespace Jigius\Soap\Core\WsdlData;

use Acc\Core\PrinterInterface;
use Acc\Core\Log\LogInterface;
use LogicException;

/**
 * Class DumbWsdlData
 *
 * @package Jigius\Soap\Core\WsdlData
 */
final class DumbWsdlData implements WsdlDataInterface
{
    /**
     * DumbAction constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function withLog(LogInterface $log): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withName(string $name): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function executed(PrinterInterface $p, $r = null): ResultInterface
    {
        throw new LogicException("Just a stub");
    }
}
