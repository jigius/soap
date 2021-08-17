<?php
declare(strict_types=1);

namespace Jigius\Soap\Core\Printer;

use Acc\Core\PrinterInterface;
use DateTime;
use DateTimeInterface;
use DomainException;

/**
 * Class WsdlDocument
 *
 * @package Jigius\Soap\Core\Printer
 */
final class WsdlDocument implements PrinterInterface
{
    /**
     * @var string
     */
    private string $serverUrl;
    /**
     * @var string
     */
    private string $targetNS;
    /**
     * @var array
     */
    private array $opts;
    /**
     * @var KeyValStorage|KeyValStorageInterface
     */
    private KeyValStorageInterface $kvStorage;
    /**
     * @var array[]
     */
    private array $data;

    /**
     * WsdlDocument constructor.
     *
     * @param string $serverUrl
     * @param string $targetNS
     * @param array $opts
     * @param KeyValStorageInterface|null $kvStorage
     */
    public function __construct(
        string $serverUrl,
        string $targetNS,
        array $opts = [],
        ?KeyValStorageInterface $kvStorage = null
    ) {
        $this->serverUrl = $serverUrl;
        $this->targetNS = $targetNS;
        $this->opts = $opts;
        $this->kvStorage = $kvStorage ?? new KeyValStorage();
        $this->data = [
            'type' => [],
            'binding' => [],
            'message' => [],
            'portType' => []
        ];
    }

    public function with($key, $val): self
    {
        $this->validate($key, $val);
        $obj = new self(
            $this->serverUrl,
            $this->targetNS,
            $this->opts,
            $this->kvStorage->append($key, $val['name'], md5($val['pcdata'])));
        $obj->data = $this->data;
        $obj->data[$key][$val['name']] = $val['pcdata'];
        return $obj;
    }

    private function validate($key, $value)
    {
        if (!isset($this->data[$key])) {
            throw new DomainException("data key is unknown");
        }
        if ($this->kvStorage->known($key, $value['name'])) {
            $hash = md5($value['pcdata']);
            if ($this->kvStorage->value($key, $value['name']) != $hash) {
                throw new DomainException("dup data is detected! key={$key}, name={$value['name']}");
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function finished(): void
    {
        $output = [];
        $output[] = <<< EOT
<?xml version="1.0" encoding="UTF-8"?>
  <definitions name="ExchangeDataService"
    targetNamespace="{$this->targetNS}"
    xmlns="http://schemas.xmlsoap.org/wsdl/"
    xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:#ns1#="{$this->targetNS}"
    xmlns:#ns#="http://www.w3.org/2001/XMLSchema"
  >
  <types>
    <#ns#:schema elementFormDefault="qualified" targetNamespace="{$this->targetNS}">
EOT;
        foreach ($this->data['type'] as $chunk) {
            $output[] = $chunk;
        }
        $output[] = <<< EOT
    </#ns#:schema>
  </types>
EOT;
        foreach ($this->data['message'] as $chunk) {
            $output[] = $chunk;
        }
        $output[] = <<< EOT
  <binding name="ExchangeDataServiceSoap" type="#ns1#:ExchangeDataServiceSoap">
    <soap:binding transport="http://schemas.xmlsoap.org/soap/http"/>
EOT;
        foreach ($this->data['binding'] as $chunk) {
            $output[] = $chunk;
        }
        $output[] = <<< EOT
  </binding>
  <portType name="ExchangeDataServiceSoap">
EOT;
        foreach ($this->data['portType'] as $chunk) {
            $output[] = $chunk;
        }
        $descr = htmlspecialchars(
            $this->opts['description'] ?? "Сервис обмена данными с 1С-сервером",
            ENT_XML1,
            "UTF-8",
            false
        ) .
            " GNRTD: " . (new DateTime())->format(DateTimeInterface::ATOM);
        $output[] = <<< EOT
  </portType>
  <service name="ExchangeDataService">
    <documentation xmlns="http://schemas.xmlsoap.org/wsdl/">{$descr}</documentation>
    <port binding="#ns1#:ExchangeDataServiceSoap" name="ExchangeDataServiceSoap">
      <soap:address location="{$this->serverUrl}"/>
    </port>
  </service>
</definitions>
EOT;
        echo preg_replace(
            [
                "/#ns#/",
                "/#ns1#/"
            ],
            [
                $this->opts['ns'] ?? "xsd",
                $this->opts['ns1'] ?? "tns",
            ],
            implode("\n", $output)
        );
    }
}
