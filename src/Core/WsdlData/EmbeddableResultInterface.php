<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\WsdlData;

use Acc\Core\PrinterInterface;
use Acc\Core\Log;

/**
 * Interface EmbeddableResultInterface
 *
 * @package Jigius\Soap\Core\WsdlData
 */
interface EmbeddableResultInterface
{
    /**
     * @param Log\LogInterface $log
     * @return EmbeddableResultInterface
     */
    public function withLog(Log\LogInterface $log): EmbeddableResultInterface;

    /**
     * @param PrinterInterface $p
     * @return EmbeddableResultInterface
     */
    public function withPrinter(PrinterInterface $p): EmbeddableResultInterface;
}
