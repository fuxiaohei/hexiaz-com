<?php


/**
 * Class blogStore
 */
class blogStore {

    /**
     * @var int
     */
    public $node_id;

    /**
     * @var string
     */
    public $node_key;

    /**
     * @var string
     */
    public $link;

    /**
     * @var string
     */
    public $persist_link;

    /**
     * @var int
     */
    public $create_time;

    /**
     * @var string
     */
    public $category_key;

    /**
     * @var string
     */
    public $category_link;

    /**
     *
     */
    public function __construct() {
        $this->link = 'p/' . $this->node_key . '.html';
        $this->persist_link = 'p/?id=' . $this->node_id;
        if ($this->create_time) {
            $this->create_time_format = date('m.d', $this->create_time);
        }
        if ($this->category_key) {
            $this->category_link = 'c/' . $this->category_key . '/';
        }
    }


}