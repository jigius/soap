<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task;

use Jigius\Soap\Core;
use Acc\Core\Log;
use LogicException;

/**
 * Class ResponseWithXMLFault
 *
 * @package Jigius\Soap\App\Task
 */
final class ResponseWithXMLFault implements Core\Task\TaskInterface
{
    /**
     * @var bool
     */
    private bool $executed;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;
    /**
     * @var string
     */
    private string $fString;
    /**
     * @var string
     */
    private string $fCode;

    /**
     * ResponseWithFault constructor.
     *
     * @param string $faultString
     * @param string $faultCode
     */
    public function __construct(string $faultString, string $faultCode = "SOAP-ENV:Server")
    {
        $this->log = new Log\NullLog();
        $this->fString = $faultString;
        $this->fCode = $faultCode;
        $this->executed = false;
    }

    /**
     * @inheritDoc
     */
    public function executed(?Core\Task\EmbeddableResultInterface $r = null): Core\Task\ResultInterface
    {
        if ($this->executed) {
            throw new LogicException("task has being already executed");
        }
        $fCode = htmlentities($this->fCode);
        $fString = htmlentities($this->fString);
        echo <<< EOT
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <SOAP-ENV:Fault>
      <faultcode>{$fCode}</faultcode>
      <faultstring>{$fString}</faultstring>
    </SOAP-ENV:Fault>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOT;
        $obj = $this->blueprinted();
        $obj->executed = true;
        return
            ($r ?? new Result())
                ->withLog($this->log)
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
        $obj = new self($this->fString, $this->fCode);
        $obj->log = $this->log;
        $obj->executed = $this->executed;
        return $obj;
    }
}
