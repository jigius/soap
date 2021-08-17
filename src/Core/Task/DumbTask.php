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
use LogicException;

/**
 * Class DumbTask
 *
 * @package Jigius\Soap\Core\Task
 */
final class DumbTask implements TaskInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function withLog(LogInterface $log): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function executed(?EmbeddableResultInterface $r = null): ResultInterface
    {
        throw new LogicException("Just a stub");
    }
}
