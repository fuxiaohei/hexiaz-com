<?php

class Error {

    public static function stop($message, $type) {
        var_dump(func_get_args());
        exit;
    }
}
