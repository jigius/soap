<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Action;

use Acc\Core\Log\LogInterface;
use Jigius\Soap\Core\EmbeddableLogInterface;

/**
 * Interface EmbeddableResultInterface
 *
 * @package Jigius\Soap\Core\Action
 */
interface EmbeddableResultInterface extends EmbeddableLogInterface
{
    /**
     * @param object $val
     * @return EmbeddableResultInterface
     */
    public function withResponse(object $val): EmbeddableResultInterface;

    /**
     * @param LogInterface $log
     * @return EmbeddableResultInterface
     */
    public function withLog(LogInterface $log): EmbeddableResultInterface;
}
