<?php

/**
 *  视图类
 */
class View {

    /**
     * 视图配置数据
     * @var array
     */
    public static $option = array();

    /**
     * 缓存对象
     * @var FileCache|AbstractCache|null
     */
    private static $cacheObject;

    /**
     * 初始化视图类
     * @param array $option 自定义配置，覆盖默认配置
     */
    public static function init($option = array()) {
        self::$option = HeXi::$config['app']['view'];
        self::$option = $option + self::$option;
        if (!self::$option) {
            HeXi::exception('View Options are lost', __CLASS__);
        }
        #处理缓存对象要求
        if (self::$option['cache']) {
            self::$enableCache = is_null(self::$enableCache) ? true : self::$enableCache;
            self::$cacheObject = Cache::get('view');
            if (!self::$cacheObject) {
                self::$enableCache = false;
            }
        }
    }

    /**
     * 调用的Widget
     * @var array
     */
    public static $widget = array();

    /**
     * 调用的Data
     * @var array
     */
    public static $data = array();

    /**
     * 调用的嵌入tpl
     * @var array
     */
    public static $import = array();

    /**
     * 编译字符串
     * @param string $string
     * @return mixed
     */
    public static function compile($string) {
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
     *  是否缓存，同时也是缓存标识
     * @var null|string|bool
     */
    public static $enableCache = null;

    /**
     * 渲染页面
     * @param string $tpl
     * @return string
     */
    public static function render($tpl) {
        #没有初始化，就init一下
        if (!self::$option) {
            view::init();
        }
        #获取模板绝对地址
        $tpl = HeXi::$path . View::$option['path'] . $tpl;
        #获取缓存数据
        if (self::$enableCache && self::$cacheObject) {
            $data = self::$cacheObject->get($tpl . self::$enableCache);
            if ($data) {
                return $data;
            }
        }
        #模板丢失
        if (!is_file($tpl)) {
            HeXi::exception('Template File is lost: ' . $tpl);
        }
        #如果开启编译，就获取编译数据
        if (View::$option['compile']) {
            $compiled = HeXi::$path . View::$option['compile']['path'] . md5($tpl) . '.php';
            #没有编译过，编译一下
            if (!is_file($compiled)) {
                file_put_contents($compiled, self::parseTpl($tpl));
            }
            #编译过期了，编译一下
            if (filemtime($compiled) + View::$option['compile']['expire'] < NOW) {
                file_put_contents($compiled, self::parseTpl($tpl));
            }
            #使用编译文件作为实际执行文件
            $real = $compiled;
        } else {
            #不编译，就直接执行当前模板
            $real = $tpl;
        }
        ob_start();
        #展开数据
        extract(self::$data);
        include $real;
        $content = ob_get_clean();
        #缓存渲染结果
        if (self::$enableCache && self::$cacheObject) {
            #标记缓存时间
            $content .= PHP_EOL . '<!-- cached at ' . date('Y-m-d H:i:s', NOW) . ' -->';
            self::$cacheObject->put($tpl . self::$enableCache, $content);
        }
        return $content;
    }

    /**
     * 解析模板内容
     * @param string $tpl
     * @return string
     */
    private static function parseTpl($tpl) {
        $string = file_get_contents($tpl);
        #处理嵌入问题
        foreach (self::$import as $name => $import) {
            $import = HeXi::$path . View::$option['path'] . $import;
            if (is_file($import)) {
                $string = str_replace('<!--include:' . $name . '-->', file_get_contents($import), $string);
            } else {
                $string = str_replace('<!--include:' . $name . '-->', '<!-- include "' . $name . '" not found -->', $string);
            }
        }
        #处理Widget问题
        foreach (self::$widget as $name => $widget) {
            if (is_string($widget)) {
                $string = str_replace('<!--widget:' . $name . '-->', $widget, $string);
            } else {
                $string = str_replace('<!--widget:' . $name . '-->', '<!-- widget "' . $name . '" fail -->', $string);
            }
        }
        #编译模板
        $compileTime = PHP_EOL . '<!-- compiled at ' . date('Y-m-d H:i:s', NOW) . ' -->';
        return self::compile($string) . $compileTime;
    }


    /**
     * 构建视图类
     * @param array $build
     */
    public static function build($build = array()) {
        if (is_array($build['import'])) {
            self::$import = $build['import'] + self::$import;
        }
        if (is_array($build['widget'])) {
            self::$widget = $build['widget'] + self::$widget;
        }
        if (is_array($build['data'])) {
            self::$data = $build['data'] + self::$data;
        }
        if ($build['cache']) {
            self::$option['cache'] = $build['cache'];
        }
    }

    /**
     * 清理视图临时数据，缓存和编译模板
     * @param string $name
     * @return bool
     */
    public static function clear($name = 'all') {
        if (!self::$option) {
            self::init();
        }
        if ($name == 'all' || $name == 'compile') {
            if (self::$option['compile']) {
                $files = glob(HeXi::$path . View::$option['compile']['path'] . '*.php');
                foreach ($files as $file) {
                    unlink($file);
                }
            }
        }
        if ($name == 'all' || $name == 'cache') {
            if (self::$cacheObject) {
                self::$cacheObject->clear();
            }
        }
        return true;
    }

}
