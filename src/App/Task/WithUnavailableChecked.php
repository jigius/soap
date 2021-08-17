<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task;

use Jigius\Soap\Core;
use Acc\Core\Log;

/**
 * Class WithUnavailableChecked
 *
 * @package Jigius\Soap\App\Task
 */
final class WithUnavailableChecked implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var string
     */
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var string
     */
    private string $pathfile;
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $na;
    /**
     * @var Core\Task\TaskInterface
     */
    private Core\Task\TaskInterface $a;

    /**
     * WithUnavailableChecked constructor.
     *
     * @param Core\Task\TaskInterface $available
     * @param Core\Task\TaskInterface $notAvailable
     * @param string $pathfile
     */
    public function __construct(
        Core\Task\TaskInterface $available,
        Core\Task\TaskInterface $notAvailable,
        string $pathfile)
    {
        $this->a = $available;
        $this->na = $notAvailable;
        $this->pathfile = $pathfile;
        $this->log = new Log\NullLog();
        $this->executed = false;
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        $obj = $this->blueprinted();
        if (is_readable($this->pathfile)) {
            $excd =
                $this
                    ->na
                    ->withLog($this->log)
                    ->executed($r);
            $obj->na = $excd->task();
        } else {
            $excd =
                $this
                    ->a
                    ->withLog($this->log)
                    ->executed($r);
            $obj->a = $excd->task();
        }
        $obj->log = $excd->log();
        $obj->executed = true;
        return
            ($r ?? new Result())
                ->withLog($obj->log)
                ->withTask($obj);
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
        $obj = new self($this->a, $this->na, $this->pathfile);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
