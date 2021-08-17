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
 * WSDL file implementation is used by SOAP-server
 */
use Acc\Core\Log;
use Jigius\Soap\App\Task;
use Jigius\Soap\App\Task\File\LockedFile;

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
    if (!$tag || !preg_match('/^[a-z0-9_.-]{1,16}$/i', $tag)) {
        throw new InvalidArgumentException("param `key` is invalid", 400);
    }
    if (!$cfg->get($tag)) {
        throw new InvalidArgumentException("param `key` is unknown", 422);
    }
    $wsdlFile = $cfg->get("cache.folder") . "/{$tag}.wsdl";
    $tmpFile = $cfg->get("tmpFolder") . "/wsdl__{$tag}.tmp";
    $cacheTTL = $cfg->get("cache.ttl");
    $mutex =
        (new LockedFile())
            ->withPath(sys_get_temp_dir() . "/wsdl.lck");
    $app =
        (/*new Task\Http\WithBaseAuthenticated(*/
            new Task\WithUnavailableChecked(
                new Task\Http\AddedHeaders(
                    new Task\File\WithCaching(
                        new Task\File\WithLockedFile(
                            new Task\File\WithRenamedFile(
                                new Task\File\Tee(
                                    new Task\NewWsdlDocument(
                                        /*
                                         * FIXME! переделать на использование PSR7
                                         */
                                        "http" . (!empty($_SERVER['HTTPS'])? "s": "") . "://" .
                                            $_SERVER['HTTP_HOST'] .
                                            ($_SERVER['SERVER_PORT'] != 80?
                                                ":" . $_SERVER['SERVER_PORT']:
                                                ""
                                            ) .
                                            "/server.php?key={$tag}",
                                        $cfg->get("{$tag}.targetNS"),
                                        $cfg->get("{$tag}.operations"),
                                        [
                                            'description' => $cfg->get("{$tag}.description"),
                                            'ns' => $cfg->get("{$tag}.ns"),
                                            'ns1' => $cfg->get("{$tag}.ns1")
                                        ]
                                    ),
                                    $tmpFile
                                ),
                                $tmpFile,
                                $wsdlFile
                            ),
                            (new LockedFile())
                                ->withPath(sys_get_temp_dir() . "/wsdl.lck")
                        ),
                        new Task\File\Cated($wsdlFile),
                        $wsdlFile,
                        $cacheTTL
                    ),
                    [
                        "Content-Type: text/xml; charset=utf-8",
                        "Cache-Control:" .
                            " max-age=" .
                                (is_readable($wsdlFile)?
                                    filemtime($wsdlFile) + $cacheTTL - time():
                                    $cacheTTL
                                ) .
                            ", must-revalidate"
                    ]
                ),
                new Task\Http\AddedHeaders(
                    new Task\ResponseWithHtmlFault(
                        "The requested service is temporarily unavailable (turned off). Do the request later.",
                        503
                    ),
                    [
                        "Content-Type: text/html; charset=utf-8"
                    ],
                ),
                __DIR__ . "/unavailable.soap"
            )/*,
            $cfg->get("{$tag}.auth.baseAuth", "")
        )*/)
    ->withLog($log)
    ->executed();
    ob_flush();
} catch (InvalidArgumentException $ex) {
    ob_get_clean();
    (new Task\Http\AddedHeaders(
        new Task\Nop(),
        [
            "Content-Type: text/html; charset=utf-8"
        ]
    ))->executed();
    echo $ex->getMessage();
    http_response_code($ex->getCode());
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
                ->withText("Tag of an error is `{$tag}`")
        )
        ->withEntry(
            (new Log\LogExceptionEntry())
                ->withLevel(
                    new Log\LogLevel(Log\LogLevelInterface::DEBUG)
                )
                ->withException($ex)
        );
    (new Task\Http\AddedHeaders(
        new Task\ResponseWithXMLFault( "This error is tagged with tag=`{$tag}` on the site side"),
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
