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
