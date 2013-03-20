<?php

/**
 * 视图类
 * Class View
 */
class View {

    /**
     * 创建视图类
     * @param string $path
     * @param int $compile
     * @param array $data
     * @return View
     */
    public static function make($path, $compile = 1, $data = array()) {
        return new View($path, $compile, $data);
    }

    /**
     * 视图文件夹地址
     * @var string
     */
    protected $path;

    /**
     * 视图数据
     * @var array
     */
    protected $data;

    /**
     * 是否编译，编译过期时间
     * @var int
     */
    protected $compile;

    /**
     * 构造方法
     * @param string $path
     * @param int $compile
     * @param array $data
     */
    private function __construct($path, $compile, $data) {
        $this->path = $path;
        $this->data = $data;
        $this->compile = $compile;
    }

    /**
     * 添加数据
     * @param string|array $name
     * @param mixed $value
     * @return $this
     */
    public function with($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->data[$k] = $v;
            }
            return $this;
        }
        $this->data[$name] = $value;
        return $this;
    }


    /**
     * 静态编译字符串
     * @param View $view
     * @param string $string
     * @return string
     */
    private function compile($view, $string) {
        #获取匹配布局内容
        $pattern = '/<!--include:(.*)-->/';
        #没有匹配，返回字符串
        if (preg_match_all($pattern, $string, $matches)) {
            $matches[0] = array_unique($matches[0]);
            $matches[1] = array_unique($matches[1]);
            $subTpl = array_combine($matches[0], $matches[1]);
            #循环布局元素
            foreach ($subTpl as $str => $tpl) {
                #判断是不是已经提交的视图
                $file = $view->path . $tpl;
                if (is_file($file)) {
                    $string = str_replace($str, file_get_contents($file), $string);
                }
            }
        }
        $string = str_replace('<!--foreach(', '<?php foreach(', $string);
        $string = str_replace('<!--for(', '<?php for(', $string);
        $string = str_replace(')-->', '){ ?>', $string);
        $string = str_replace(array('<!--/foreach-->', '<!--/for-->', '<!--endforeach-->', '<!--endfor-->'), '<?php } ?>', $string);
        $string = str_replace('<!--if(', '<?php if(', $string);
        $string = str_replace('<!--elseif(', '<?php }elseif(', $string);
        $string = str_replace('<!--else-->', '<?php }else{ ?>', $string);
        $string = str_replace(array('<!--/if-->', '<!--endif-->'), '<?php } ?>', $string);
        $string = str_replace('{{', '<?php echo ', $string);
        $string = str_replace('<!--{', '<?php ', $string);
        $string = str_replace(array('}-->', '}}'), ' ?>', $string);
        return $string;
    }

    /**
     * 处理编译文件
     * 判断是编译还是用缓存
     * @param string $file
     * @return string
     */
    private function doCompile($file) {
        $compiled = $this->path . 'compiled/' . md5($file) . '.php';
        if (is_file($compiled)) {
            #如果编译缓存有效，返回它
            if (filemtime($compiled) + $this->compile > time()) {
                return $compiled;
            }
        }
        $string = self::compile($this, file_get_contents($file));
        file_put_contents($compiled, $string);
        return $compiled;
    }


    /**
     * 渲染文件
     * @param string $template
     * @return string
     * @throws Exception
     */
    public function render($template) {
        $file = $this->path . $template;
        if (!is_file($file)) {
            throw new Exception('无法渲染文件"' . $template . '"');
        }
        if ($this->compile > 0) {
            $file = $this->doCompile($file);
        }
        ob_start();
        extract($this->data);
        include_once $file;
        return ob_get_clean();
    }

}