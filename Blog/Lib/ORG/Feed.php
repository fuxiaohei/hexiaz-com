<?php

/**
 * Feed订阅XML生成类
 * Class Feed
 */
class Feed {

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
     * 结束XML文档并输出
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
     * 写订阅Meta信息
     * @param array $meta
     */
    private static function meta($meta) {
        self::$writer->writeElement('title', $meta['title']);
        self::$writer->writeElement('link', $meta['link']);
        self::$writer->writeElement('description', $meta['description']);
        self::$writer->writeElement('language', $meta['language']);
        self::$writer->writeElement('pubDate', date(DATE_RSS));
    }

    /**
     * 写订阅项目
     * @param object $item
     * @param string $baseUrl
     */
    private static function item($item,$baseUrl) {
        self::$writer->startElement('item');
        self::$writer->writeElement('title',$item->node_title);
        self::$writer->writeElement('link',$baseUrl.$item->link);
        self::$writer->writeElement('category',$item->category_name);
        self::$writer->startElement('description');
        self::$writer->writeCdata($item->node_content);
        self::$writer->endElement();
        self::$writer->writeElement('pubDate',date(DATE_RSS,$item->create_time));
        self::$writer->writeElement('guid',$baseUrl.$item->persist_link);
        self::$writer->endElement();
    }

    /**
     * 渲染所有内容
     * @param array $meta
     * @param array $items
     * @return bool|string
     */
    public static function render($meta, $items) {
        self::start();
        self::$writer->startElement('rss');
        self::$writer->writeAttribute('version', '2.0');
        self::$writer->startElement('channel');
        self::meta($meta);
        foreach ($items as $item) {
            self::item($item,$meta['link']);
        }
        self::$writer->endElement();
        self::$writer->endElement();
        return self::end();
    }
}