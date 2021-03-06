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

namespace Jigius\Soap\App\Action;

use Acc\Core\Log;
use Jigius\Soap\Core;
use Throwable;

/**
 * Class WithExtraLogging
 *
 * @package Jigius\Soap\App\Action
 */
final class WithExtraLogging implements Core\Action\ActionInterface
{
    /**
     * @var Core\Action\ActionInterface
     */
    private Core\Action\ActionInterface $original;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var string
     */
    private string $info;

    /**
     * LoggedAction constructor.
     *
     * @param Core\Action\ActionInterface $action
     * @param string $info
     */
    public function __construct(Core\Action\ActionInterface $action, string $info)
    {
        $this->original = $action;
        $this->log = new Log\NullLog();
        $this->info = $info;
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function executed(array $args, ?Core\Action\EmbeddableResultInterface $r = null): Core\Action\ResultInterface
    {
        $log = $this->log;
        try {
            $start = $this->mt();
            $result = $this->original->executed($args, $r);
            $ms = ceil(($this->mt() - $start) / 1000);
            $log
                ->withEntry(
                    (new Log\LogTextEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withText($this->info)
                )
                ->withEntry(
                    (new Log\LogTextEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withText($this->info)
                        ->withText(
                            sprintf("?????????? ???????????????????? %sms", $ms > 0? $ms: "<1")
                        )
                )
                ->withEntry(
                    (new Log\LogTextEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withText("?????????????? ??????????????????:")
                )
                ->withEntry(
                    (new Log\LogArrayEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withArray($args)
                )
                ->withEntry(
                    (new Log\LogTextEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withText("??????????????????:")
                )
                ->withEntry(
                    (new Log\LogArrayEntry())
                        ->withLevel(
                            new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                        )
                        ->withArray(
                            (array)$result->response()
                        )
                );
            return $result->withLog($this->log);
        } catch (Throwable $ex) {
            $log =
                $log
                    ->withEntry(
                        (new Log\LogTextEntry())
                            ->withLevel(
                                new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                            )
                            ->withText($this->info)
                    )
                    ->withEntry(
                        (new Log\LogTextEntry())
                            ->withLevel(
                                new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                            )
                            ->withText($this->info)
                            ->withText("?????????????? ??????????????????:")
                    )
                    ->withEntry(
                        (new Log\LogArrayEntry())
                            ->withLevel(
                                new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                            )
                            ->withArray($args)
                    );
            throw $ex;
        }
    }

    /**
     * @inheritDoc
     */
    public function withLog(Log\LogInterface $log): self
    {
        $obj = $this->blueprinted();
        $obj->log = $log;
        return $obj;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self($this->original, $this->info);
        $obj->log = $this->log;
        return $obj;
    }

    /**
     * @return int
     */
    private function mt(): int
    {
        list($usec, $sec) = explode(" ", microtime());
        return (int)($sec * 1000000 + $usec);
    }
}
