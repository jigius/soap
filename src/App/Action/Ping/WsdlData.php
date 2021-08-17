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

namespace Jigius\Soap\App\Action\Ping;

use Acc\Core\PrinterInterface;
use Jigius\Soap\App\WsdlData\Result;
use Jigius\Soap\Core\WsdlData\WsdlDataInterface;
use Acc\Core\Log;
use Jigius\Soap\Core;

/**
 * Class WsdlData
 *
 * @package Jigius\Soap\App\Action\Ping
 */
final class WsdlData implements WsdlDataInterface
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var Log\LogInterface
     */
    private Log\LogInterface $log;

    /**
     * WsdlData constructor.
     */
    public function __construct()
    {
        $this->name = "unknown";
        $this->log = new Log\NullLog();
    }

    /**
     * @inheritDoc
     */
    public function executed(
        PrinterInterface $p,
        ?Core\WsdlData\EmbeddableResultInterface $r = null
    ): Core\WsdlData\ResultInterface {
        $type = [];
        $type[] = [
            "name" => "payloadType",
            "pcdata" => <<< EOT
<#ns#:simpleType name="payloadType">
    <#ns#:annotation>
        <#ns#:documentation>Произвольная строка &quot;нагрузка&quot;</#ns#:documentation>
    </#ns#:annotation>
        <#ns#:restriction base="#ns#:string">
    </#ns#:restriction>
</#ns#:simpleType>
EOT
        ];
        $type[] = [
            "name" => "payload",
            "pcdata" => <<< EOT
<#ns#:element name="payload" type="#ns1#:payloadType"></#ns#:element>
EOT
        ];
        $type[] = [
            "name" => "{$this->name}Response",
            "pcdata" => <<< EOT
<#ns#:element name="{$this->name}Response">
    <#ns#:annotation>
        <#ns#:documentation>
            Ответ на запрос
        </#ns#:documentation>
    </#ns#:annotation>
    <#ns#:complexType>
        <#ns#:sequence>
            <#ns#:element minOccurs="1" maxOccurs="1" name="errorCode" type="#ns#:string"></#ns#:element>
            <#ns#:element minOccurs="0" maxOccurs="1" name="errorText" type="#ns#:string"></#ns#:element>
            <#ns#:element minOccurs="0" maxOccurs="1" name="value" type="#ns1#:payloadType"></#ns#:element>
        </#ns#:sequence>
    </#ns#:complexType>
</#ns#:element>
EOT
        ];
        $message = [];
        $message[] = [
            "name" => "{$this->name}SoapIn",
            "pcdata" => <<< EOT
<message name="{$this->name}SoapIn">
    <part name="payload" element="#ns1#:payload"/>
</message>
EOT
        ];
        $message[] = [
            "name" => "{$this->name}SoapOut",
            "pcdata" => <<< EOT
<message name="{$this->name}SoapOut">
    <part name="parameters" element="#ns1#:{$this->name}Response"/>
</message>
EOT
        ];
        $binding = [];
        $binding[] = [
            "name" => "{$this->name}",
            "pcdata" => <<< EOT
<operation name="{$this->name}">
    <soap:operation soapAction="{$this->name}" style="document"/>
    <input>
        <soap:body use="literal"/>
    </input>
    <output>
        <soap:body use="literal"/>
    </output>
</operation>
EOT
        ];
        $portType = [];
        $portType[] = [
            "name" => "{$this->name}",
            "pcdata" => <<< EOT
<operation name="{$this->name}">
    <documentation xmlns="http://schemas.xmlsoap.org/wsdl/">Проверка сервиса</documentation>
    <input message="#ns1#:{$this->name}SoapIn"/>
    <output message="#ns1#:{$this->name}SoapOut"/>
</operation>
EOT
        ];
        foreach ($type as $typ) {
            $p = $p->with('type', $typ);
        }
        foreach ($binding as $bnd) {
            $p = $p->with('binding', $bnd);
        }
        foreach ($message as $msg) {
            $p = $p->with('message', $msg);
        }
        foreach ($portType as $port) {
            $p = $p->with('portType', $port);
        }
        return
            ($r ?? new Result())
                ->withLog($this->log)
                ->withPrinter($p);
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
    public function withName(string $name): self
    {
        $obj = $this->blueprinted();
        $obj->name = $name;
        return $obj;
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->name = $this->name;
        $obj->log = $this->log;
        return $obj;
    }
}
