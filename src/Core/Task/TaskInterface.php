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

namespace Jigius\Soap\Core\Task;

use Acc\Core\Log\LogInterface;
use Jigius\Soap\Core\EmbeddableLogInterface;

/**
 * Interface TaskInterface
 *
 * @package Jigius\Soap\Core\Task
 */
interface TaskInterface extends EmbeddableLogInterface
{
    /**
     * @param EmbeddableResultInterface|null $r
     * @return ResultInterface
     */
   public function executed(?EmbeddableResultInterface $r = null): ResultInterface;

    /**
     * @inheritDoc
     * @return TaskInterface
     */
   public function withLog(LogInterface $log): TaskInterface;
}
