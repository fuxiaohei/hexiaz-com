<?php

/**
 *
 */
class Input {

    /**
     * @var Input
     */
    private static $self;

    /**
     * @return Input
     */
    public static function instance() {
        return !self::$self ? self::$self = new Input() : self::$self;
    }


    /**
     * @param bool|string $all
     * @return mixed
     */
    public static function get($all = true) {
        if ($all === true) {
            return $_GET;
        }
        return $_GET[$all];
    }

    /**
     * @param bool|string $all
     * @return mixed
     */
    public static function post($all = true) {
        if ($all === true) {
            return $_POST;
        }
        return $_POST[$all];
    }

    /**
     * @return array
     */
    public static function all() {
        return $_REQUEST;
    }

    /**
     * @param array $arr
     * @return array
     */
    public static function to($arr = array()) {
        if (func_num_args() > 1) {
            $arr = func_get_args();
        }
        $data = array();
        foreach ($arr as $a) {
            if (isset($_REQUEST[$a])) {
                $data[$a] = $_REQUEST[$a];
            }
        }
        return $data;
    }

    /**
     * @param array $arr
     * @param array $default
     * @return array
     */
    public static function build($arr, $default = array()) {
        $input = self::to($arr);
        return $input + $default;
    }
}
