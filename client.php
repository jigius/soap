<?php
/*
 * A simple test of the soap procedure `ping`
 */
$client = new SoapClient("http://127.0.0.1/wsdl.php?key=foo");
var_dump($client->__getFunctions());
var_dump($client->ping("ddd"));
echo "\n\n====\n\nA result of the request for `ping` procedure:\n\n";
var_dump($client->ping("ping@" . rand(0, 100000)));
