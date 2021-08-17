<?php
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
