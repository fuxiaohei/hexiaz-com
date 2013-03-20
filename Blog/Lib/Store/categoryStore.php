<?php


/**
 * Class categoryStore
 */
class categoryStore {

    /**
     * @var string
     */
    public $category_link;

    /**
     * @var string
     */
    public $category_key;

    /**
     * @var string
     */
    public $create_time_format;

    /**
     * @var int
     */
    public $create_time;

    /**
     *
     */
    public function __construct() {
        $this->category_link = 'c/' . $this->category_key . '/';
        if ($this->create_time) {
            $this->create_time_format = date('M-d H:i', $this->create_time);
        }
    }

}