<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\WsdlData;

use Acc\Core\PrinterInterface;
use Acc\Core\Log;

/**
 * Interface ResultInterface
 *
 * @package Jigius\Soap\Core\WsdlData
 */
interface ResultInterface
{
    /**
     * @return Log\LogInterface
     */
    public function log(): Log\LogInterface;

    /**
     * @return PrinterInterface
     */
    public function printer(): PrinterInterface;
}
