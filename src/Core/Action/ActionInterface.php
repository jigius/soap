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

namespace Jigius\Soap\Core\Action;

use Acc\Core\Log\LogInterface;
use Jigius\Soap\Core\EmbeddableLogInterface;

/**
 * Interface ActionInterface
 *
 * @package Jigius\Soap\Core\Action
 */
interface ActionInterface extends EmbeddableLogInterface
{
    /**
     * @param array $args
     * @param EmbeddableResultInterface|null $r
     * @return ResultInterface
     */
    public function executed(array $args, ?EmbeddableResultInterface $r = null): ResultInterface;

    /**
     * @param LogInterface $log
     * @return ActionInterface
     */
    public function withLog(LogInterface $log): ActionInterface;
}
