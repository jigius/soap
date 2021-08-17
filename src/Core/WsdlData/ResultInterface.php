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
