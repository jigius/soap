<?php
declare(strict_types=1);

namespace Jigius\Soap\App\WsdlData;

use Acc\Core\PrinterInterface;
use Acc\Core\Vanilla\ArrayPrinter;
use Jigius\Soap\Core\WsdlData\EmbeddableResultInterface;
use Jigius\Soap\Core\WsdlData\ResultInterface;
use Acc\Core\Log;

/**
 * Class Result
 *
 * @package Jigius\Soap\App\WsdlData
 */
final class Result implements ResultInterface, EmbeddableResultInterface
{
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var PrinterInterface
     */
    private PrinterInterface $printer;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->log = new Log\NullLog();
        $this->printer = new ArrayPrinter();
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
     * @inheritDoc
     */
    public function log(): Log\LogInterface
    {
        return $this->log;
    }

    /**
     * @inheritDoc
     */
    public function withPrinter(PrinterInterface $p): self
    {
        $obj = $this->blueprinted();
        $obj->printer = $p;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function printer(): PrinterInterface
    {
        return $this->printer;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->log = $this->log;
        return $obj;
    }
}
