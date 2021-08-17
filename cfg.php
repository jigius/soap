<?php
declare(strict_types=1);

use Jigius\Soap\App\Action;
use Acc\Core\Log;
use Acc\Core\Registry\Vanilla\Registry;

return (function (): array {
    $log =
        (new Log\TextFileLog(
            new Log\NullLog()
        ))
            ->withMinLevel(
                new Log\LogLevel(
                    Log\LogLevelInterface::DEBUG
                )
            )
            ->withFile(__DIR__ . "/soap.log");
    return [
        "debug" => true,
        "log" => $log,
        "tmpFolder" => sys_get_temp_dir(),
        "cache" => [
            "ttl" => 1800,
            "folder" => __DIR__
        ],
        'foo' => [
            "log" => $log,
            "auth" => [
                "OFFbaseAuth" => base64_encode("test:tset")
            ],
            "targetNS" => "http://example.com/",
            "description" => "A simple example of SOAP procedures",
            "ns" => "xsd",
            "ns1" => "tns",
            "operations" =>
                (new Registry())
                    ->pushed(
                        'ping',
                        (new Action\Factory())
                            ->withAction(
                                new Action\WithExtraLogging(
                                    new Action\Ping\Action(),
                                        "Procedure `ping`"
                                    )
                                )
                                ->withWsdlData(
                                    new Action\Ping\WsdlData()
                                )
                    )
        ]
    ];
}) ();
