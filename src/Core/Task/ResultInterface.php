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

/**
 * Interface ResultInterface
 *
 * @package Jigius\Soap\Core\Task
 */
interface ResultInterface
{
    /**
     * @return LogInterface
     */
    public function log(): LogInterface;

    /**
     * @return TaskInterface
     */
    public function task(): TaskInterface;
}
