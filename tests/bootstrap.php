<?php
require __DIR__ . '/../vendor/autoload.php';

use \Tracy\Debugger;

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define('TMP_DIR', '/tmp/jaroska-api-tests');

header("Content-Type: text/html; charset=UTF-8");
if (php_sapi_name() != 'cgi-fcgi') {
    Debugger::enable();
    Debugger::$strictMode = true;
    Debugger::$showLocation = true;
    Debugger::$maxDepth = 10;
}
