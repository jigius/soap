<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\WsdlData;

use Acc\Core\PrinterInterface;
use Jigius\Soap\Core\EmbeddableLogInterface;

/**
 * Interface WsdlDataInterface
 *
 * @package Jigius\Soap\Core\WsdlData
 */
interface WsdlDataInterface extends EmbeddableLogInterface
{
    /**
     * @param PrinterInterface $p
     * @param EmbeddableResultInterface|null $r
     * @return ResultInterface
     */
    public function executed(PrinterInterface $p, ?EmbeddableResultInterface $r = null): ResultInterface;

    /**
     * @param string $name
     * @return WsdlDataInterface
     */
    public function withName(string $name): WsdlDataInterface;
}
