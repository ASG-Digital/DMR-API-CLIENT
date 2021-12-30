<?php

require_once __DIR__ . '/version.php';
require_once __DIR__ . '/functions.php';

spl_autoload_register(function ($className) {
    if (substr($className, 0, strlen('ASG\DMRAPI')) == 'ASG\DMRAPI') {
        $classPath = __DIR__ . '/Classes/' . trim(str_replace(
            '\\',
            '/',
            substr($className, strlen('ASG\DMRAPI\\'))
        ), " \t\n\r\0\x0B\\/") . '.php';
        if (file_exists($classPath)) {
            include $classPath;
        }
    }
});