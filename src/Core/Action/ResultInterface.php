<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Action;

use Acc\Core\Log;

/**
 * Interface ResultInterface
 *
 * @package Jigius\Soap\Core\Action
 */
interface ResultInterface
{
    /**
     * @return Log\LogInterface
     */
    public function log(): Log\LogInterface;

    /**
     * @return object|null
     */
    public function response(): ?object;
}
