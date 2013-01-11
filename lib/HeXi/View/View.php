<?php

/**
 *
 */
class View {

    /**
     * @const int
     */
    const SIMPLE = 1;

    /**
     * @var array
     */
    protected static $views = array();

    /**
     * @param bool|string $dir
     * @param bool|int $compiler
     * @return View
     */
    public static function make($dir = false, $compiler = false) {
        if (!$dir) {
            $dir = APP_PATH . $GLOBALS['config']['app']['view']['path'];
        }
        if (!$compiler) {
            $compiler = $GLOBALS['config']['app']['view']['engine'];
            if ($compiler) {
                $compiler = $compiler['name'];
            } else {
                $compiler = 0;
            }
        }
        $hash = md5($dir . '-' . $compiler);
        if (!self::$views[$hash]) {
            self::$views[$hash] = new View($dir, $compiler);
        }
        return self::$views[$hash];
    }

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool|AbstractCompiler|SimpleCompiler
     */
    protected $compiler;

    /**
     * @param string $dir
     * @param int $compiler
     */
    public function __construct($dir, $compiler) {
        $this->path   = $dir;
        $this->engine = $this->getEngine((int)$compiler);
    }

    /**
     * @param int $compiler
     * @return bool|AbstractCompiler|SimpleCompiler
     */
    private function getEngine($compiler) {
        if ($compiler < 1) {
            return false;
        }
        if ($compiler === 1) {
            import('HeXi.View.Compiler.Simple');
            return SimpleCompiler::instance($GLOBALS['config']['app']['view']['engine']);
        }
        return false;
    }

    /**
     * @var array
     */
    private $templates = array();

    /**
     * @param string $name
     * @param string $tpl
     * @return View
     */
    public function setTpl($name, $tpl) {
        $this->templates[$name] = $tpl;
        return $this;
    }

    /**
     * @param string $name
     * @return View
     */
    public function unsetTpl($name) {
        unset($this->templates[$name]);
        return $this;
    }

    /**
     * @var array
     */
    private $viewData = array();

    /**
     * @param string|array $key
     * @param null|string $value
     * @return View
     */
    public function assign($key, $value = null) {
        if ($value === null) {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    $this->assign($k, $v);
                }
            }
            return $this;
        }
        $this->viewData[$key] = $value;
        return $this;
    }

    /**
     * @param string $file
     * @return mixed
     */
    private function compileFile($file) {
        $string = file_get_contents($file);
        foreach ($this->templates as $name => $tpl) {
            $tplFile = $this->path . $tpl . '.html';
            if (is_file($tplFile)) {
                $string = str_replace('<!--include:' . $name . '-->', file_get_contents($tplFile), $string);
            } else {
                $string = str_replace('<!--include:' . $name . '-->', '<!--' . $name . ' not found-->', $string);
            }
        }
        $string = $this->engine->compile($string);
        $this->engine->save($file, $string);
        return $string;
    }

    /**
     * @param string $tpl
     * @return string
     */
    public function fetch($tpl) {
        $file = $this->path . $tpl . '.html';
        if (!is_file($file)) {
            exit('Invalid Template File :' . $tpl . '.html');
        }
        if ($this->engine) {
            $string = $this->engine->get($file);
            if (!$string) {
                $string = $this->compileFile($file);
            }
        } else {
            $string = file_get_contents($file);
        }
        ob_start();
        extract($this->viewData);
        eval('?>' . $string);
        return ob_get_clean();
    }
}
