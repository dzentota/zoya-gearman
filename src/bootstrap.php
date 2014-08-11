<?php
defined('ZOYA_ROOT') 
    or define('ZOYA_ROOT', __DIR__);
    
error_reporting(-1);

set_error_handler(
    function ($errNo, $errStr, $errFile, $errLine) {
        throw new ErrorException($errStr, $errNo, 0, $errFile, $errLine);
    }
);

set_exception_handler(
    function (Exception $e) {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    }
);
