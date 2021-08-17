<?php
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
