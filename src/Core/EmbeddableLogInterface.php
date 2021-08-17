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

/**
 * Interface EmbeddableLogInterface
 *
 * Used for the injection of a log instance into the instance
 *
 * @package Jigius\Soap\Core
 */
interface EmbeddableLogInterface
{
    /**
     * Injects a log instance
     * @param LogInterface $log
     * @return EmbeddableLogInterface
     */
    public function withLog(LogInterface $log): EmbeddableLogInterface;
}
