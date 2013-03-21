<?php

/**
 * 网站地图的Widget
 * Class sitemapWidget
 */
class sitemapWidget extends Widget {

    /**
     * 渲染过程
     * @return bool|string
     */
    public function render() {
        Hx::import(Hx::$name . '/Lib/ORG/Sitemap');
        $links = array();
        #顶级链接
        $links[] = array('loc' => Request::baseUrl(), 'lastmod' => date(DATE_ISO8601), 'changefreq' => 'daily',
            'priority' => '1.0');
        $links[] = array('loc' => Request::baseUrl() . 'all/', 'lastmod' => date(DATE_ISO8601), 'changefreq' => 'daily',
            'priority' => '0.8');
        $links[] = array('loc' => Request::baseUrl() . 'message/', 'lastmod' => date(DATE_ISO8601), 'changefreq' => 'daily',
            'priority' => '0.8');
        #添加博客链接
        $blog = Model::exec('blog', 'getAll', array(false));
        foreach ($blog as $b) {
            $links[] = array('loc' => Request::baseUrl() . $b->link,
                'lastmod' => date(DATE_ISO8601, $b->create_time),
                'changefreq' => 'monthly',
                'priority' => '0.2'
            );
        }
        #添加分类链接
        $category = Model::exec('category', 'getAll');
        foreach ($category as $c) {
            $links[] = array('loc' => Request::baseUrl() . $c->category_link,
                'lastmod' => date(DATE_ISO8601),
                'changefreq' => 'weekly',
                'priority' => '0.3'
            );
        }
        return Sitemap::render($links, Request::baseUrl() . 'public/css/sitemap.xsl');
    }
}