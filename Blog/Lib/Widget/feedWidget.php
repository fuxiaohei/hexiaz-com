<?php

/**
 * 订阅的Widget
 * Class feedWidget
 */
class feedWidget extends Widget{

    /**
     * 渲染过程
     * @return bool|string
     */
    public function render(){
        Hx::import(Hx::$name.'/Lib/ORG/Feed');
        return Feed::render(array(
            'title'=>'傅小黑.COM',
            'link'=>Request::baseUrl(),
            'description'=>'傅小黑自己的网站',
            'language'=>'zh-CN'
        ),Model::exec('blog','getFeed'));
    }
}