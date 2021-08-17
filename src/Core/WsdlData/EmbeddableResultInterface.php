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
