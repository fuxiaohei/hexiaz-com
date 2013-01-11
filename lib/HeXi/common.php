<?php

function import($command, $stop = false) {
    if (!$GLOBALS['imported'][$command]) {
        $file = null;
        if (stripos($command, 'HeXi.') === 0) {
            $file = str_replace(array('HeXi.', '.'), array(HEXI_PATH, DS), $command) . '.php';
        }
        if (stripos($command, APP_NAME . '.') === 0) {
            $file = str_replace(array(APP_NAME . '.', '.'), array(APP_PATH . 'Lib' . DS, DS), $command) . '.php';
        }
        if (!is_file($file)) {
            if ($stop) {
                exit('Invalid Import Command: ' . $command);
            }
            return false;
        }
        require_once $file;
        $GLOBALS['imported'][$command] = $file;
    }
    return true;
}

function config($name, $overwrite = false) {
    if ($overwrite || !$GLOBALS['config'][$name]) {
        $file = APP_PATH . 'Config' . DS . $name . '.php';
        if (!is_file($file)) {
            exit('Config File "' . $name . '" is missing');
        }
        $GLOBALS['config'][$name] = require($file);
    }
}