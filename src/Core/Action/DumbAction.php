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

use LogicException;
use Acc\Core\Log\LogInterface;

/**
 * Class DumbAction
 *
 * @package Jigius\Soap\Core\Action
 */
final class DumbAction implements ActionInterface
{
    /**
     * DumbAction constructor.
     */
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
     */
    public function withName(string $name): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function executed(array $args, $r = null): ResultInterface
    {
        throw new LogicException("Just a stub");
    }
}
