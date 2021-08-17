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
/*
 * SOAP server implementation
 */
use Acc\Core\Log;
use Jigius\Soap\App;

require_once __DIR__ . "/vendor/autoload.php";

ob_start();
$log =
    (new Log\TextFileLog())
        ->withFile("php://stdout", "wb");
try {
    $cfg = Noodlehaus\Config::load(__DIR__ . "/cfg.php");
    $log = $cfg->get("log");
    /*
     * FIXME! переделать на использование PSR7
     */
    $tag = $_GET['key'] ?? "";
    if ($tag == "" || !preg_match("/^[a-z0-9_.-]{1,16}$/i", $tag)) {
        throw new InvalidArgumentException("param `key` is invalid", 400);
    }
    if (empty($cfg->get($tag))) {
        throw new InvalidArgumentException("param `key` is unknown", 422);
    }
    $result =
        (new App\Task\Http\WithBaseAuthenticated(
            new App\Task\WithUnavailableChecked(
                new App\Task\Operation(
                    new SoapServer(
                    /*
                     * FIXME! переделать на использование PSR7
                     */
                        implode(
                            "",
                            [
                                "http",
                                !empty($_SERVER['HTTPS']) ? "s" : "",
                                "://",
                                $_SERVER['HTTP_HOST'],
                                $_SERVER['SERVER_PORT'] != 80 ? ":{$_SERVER['SERVER_PORT']}" : "",
                                "/wsdl.php?key=$tag"
                            ]
                        ),
                        [
                            'soap_version' => SOAP_1_2,
                            'style' => SOAP_RPC
                        ]
                    ),
                    new App\Dispatcher($cfg->get("$tag.operations"))
                ),
                new App\Task\Http\AddedHeaders(
                    new App\Task\ResponseWithHtmlFault(
                        "The requested service is temporarily unavailable (turned off). Do the request later.",
                        503
                    ),
                    [
                        "Content-Type: text/html; charset=utf-8"
                    ],
                ),
                __DIR__ . "/unavailable.soap"
            ),
            $cfg->get("$tag.auth.baseAuth", ""),
        ))
            ->withLog($log)
            ->executed();
    $log = $result->log();
    ob_end_flush();
} catch (App\Action\ActionIsNotDefinedException $ex) {
    ob_get_clean();
    (new App\Task\Http\AddedHeaders(
        new App\Task\ResponseWithHtmlFault("Action has not being defined :/", 422),
        [
            "Content-Type: text/html; charset=utf-8"
        ]
    ))->executed();
} catch (InvalidArgumentException $ex) {
    ob_get_clean();
    (new App\Task\Http\AddedHeaders(
        new App\Task\ResponseWithHtmlFault($ex->getMessage(), $ex->getCode()),
        [
            "Content-Type: text/html; charset=utf-8"
        ]
    ))->executed();
} catch (Throwable $ex) {
    ob_get_clean();
    $tag = bin2hex(random_bytes(8));
    $log
        ->withEntry(
            (new Log\LogTextEntry())
                ->withLevel(
                    new Log\LogLevel(Log\LogLevelInterface::ERROR)
                )
                ->withText(
                    sprintf("An error is occurred: `%s`!", $ex->getMessage())
                )
        )
        ->withEntry(
            (new Log\LogTextEntry())
                ->withLevel(
                    new Log\LogLevel(Log\LogLevelInterface::ERROR)
                )
                ->withText("Tag of an error is `$tag`")
        )
        ->withEntry(
            (new Log\LogExceptionEntry())
                ->withLevel(
                    new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                )
                ->withException($ex)
        );
    (new App\Task\Http\AddedHeaders(
        new App\Task\ResponseWithXMLFault( "This error is tagged with tag=`$tag` on the site side"),
        [
            "Content-Type: text/xml; charset=utf-8"
        ]
    ))
        ->withLog($log)
        ->executed();
    $code = $ex->getCode();
    http_response_code(
        !$code? 500: $code
    );
}
