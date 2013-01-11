<?php

import('HeXi.View.Compiler.Abstract');

/**
 *
 */
class SimpleCompiler extends AbstractCompiler {

    /**
     * @var SimpleCompiler
     */
    private static $self;

    /**
     * @param array $option
     * @return SimpleCompiler
     */
    public static function instance($option = array()) {
        return !self::$self ? self::$self = new SimpleCompiler($option) : self::$self;
    }

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $expire;

    /**
     * @param array $option
     * @return SimpleCompiler
     */
    public function set($option = array()) {
        if ($option['path']) {
            $this->path = APP_PATH . $option['path'];
        }
        if ($option['expire']) {
            $this->expire = (int)$option['expire'];
        }
        return $this;
    }

    /**
     * @param string $string
     * @return string
     */
    public function compile($string) {
        $string = str_replace('<!--foreach(', '<?php foreach(', $string);
        $string = str_replace('<!--for(', '<?php for(', $string);
        $string = str_replace(')-->', '){ ?>', $string);
        $string = str_replace(array('<!--/foreach-->', '<!--/for-->'), '<?php } ?>', $string);
        $string = str_replace('<!--if(', '<?php if(', $string);
        $string = str_replace('<!--elseif(', '<?php }elseif(', $string);
        $string = str_replace('<!--else-->', '<?php }else{ ?>', $string);
        $string = str_replace('<!--/if-->', '<?php } ?>', $string);
        $string = str_replace('{{', '<?php echo ', $string);
        $string = str_replace('<!--{', '<?php ', $string);
        $string = str_replace(array('}-->', '}}'), ' ?>', $string);
        return $string;
    }

    /**
     * @param string $key
     * @param string $string
     * @return SimpleCompiler
     */
    public function save($key, $string) {
        $file = $this->path . md5($key) . '.php';
        $string .= PHP_EOL . '<!-- compiled at ' . date('Y-m-d H:i:s', NOW) . '-->';
        file_put_contents($file, $string);
        return $this;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function read($key) {
        $file = $this->path . md5($key) . '.php';
        return is_file($key) ? file_get_contents($file) : null;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function get($key) {
        $file = $this->path . md5($key) . '.php';
        if (!is_file($file)) {
            return null;
        }
        if (filemtime($file) + $this->expire > NOW) {
            return file_get_contents($file);
        }
        return null;
    }

}
