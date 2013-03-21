<?php

/**
 * 网站地图XML生成类
 * Class Sitemap
 */
class Sitemap {

    /**
     * XMLWriter对象
     * @var XMLWriter
     */
    private static $writer;

    /**
     * 开始XML文档
     */
    private static function start() {
        self::$writer = new XMLWriter();
        self::$writer->openMemory();
        self::$writer->setIndent(true);
        self::$writer->setIndentString('    ');
        self::$writer->startDocument('1.0', 'UTF-8');
    }

    /**
     * 结束XML并输出
     * @return bool|string
     */
    private static function end() {
        if (self::$writer) {
            self::$writer->endDocument();
            return self::$writer->outputMemory();
        }
        return false;
    }

    /**
     * 写出一条链接
     * @param array $link
     */
    private static function link($link) {
        self::$writer->startElement('url');
        self::$writer->writeElement('loc', $link['loc']);
        self::$writer->writeElement('lastmod', $link['lastmod']);
        self::$writer->writeElement('changefreq', $link['changefreq']);
        self::$writer->writeElement('priority', $link['priority']);
        self::$writer->endElement();
    }

    /**
     * 渲染网站地图
     * @param array $links
     * @param null|string $style XSL样式表绝对地址
     * @return bool|string
     */
    public static function render($links, $style = null) {
        self::start();
        if ($style) {
            self::$writer->writePi('xml-stylesheet', 'type="text/xsl" href="' . $style . '"');
        }
        self::$writer->startElement('urlset');
        self::$writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        self::$writer->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        self::$writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($links as $link) {
            self::link($link);
        }
        self::$writer->endElement();
        return self::end();
    }
}