<?php
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
