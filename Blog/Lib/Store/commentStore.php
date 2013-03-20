<?php

/**
 * Class commentStore
 */
class commentStore {

    /**
     * @var int
     */
    public $create_time;

    /**
     * @var string
     */
    public $create_time_diff;

    /**
     * @var string
     */
    public $author_avatar;

    /**
     * @var string
     */
    public $author_email;

    /**
     * @var string
     */
    public $author_url;

    /**
     * @var int
     */
    public $comment_id;

    /**
     * @var string
     */
    public $node_key;

    /**
     * @var string
     */
    public $node_link;

    /**
     * @var string
     */
    public $content;

    /**
     *
     */
    public function __construct() {
        if ($this->create_time) {
            $this->create_time_diff = self::timeDiff($this->create_time);
        }
        if ($this->author_email) {
            $this->author_avatar = self::gravatar($this->author_email);
        }
        if (!$this->author_url) {
            $this->author_url = '#comment-' . $this->comment_id;
        }
        if($this->node_key){
            $this->node_link = 'p/' . $this->node_key . '.html';
            $this->content = strip_tags($this->content);
        }
    }

    /**
     * Gravatar头像
     * @param string $email
     * @return string
     */
    private static function gravatar($email) {
        return 'http://1.gravatar.com/avatar/' . md5($email) . '?s=50';
    }

    /**
     * 计算时间差
     * @param int $time
     * @return string
     */
    private static function timeDiff($time) {
        $diff = time() - $time;
        if ($diff > 3600 * 24 * 30) {
            return (int)($diff / 3600 / 24 / 30) . ' 月前';
        }
        if ($diff > 3600 * 24) {
            return (int)($diff / 3600 / 24) . ' 天前';
        }
        if ($diff > 3600) {
            return (int)($diff / 3600) . ' 小时前';
        }
        if ($diff > 60) {
            return (int)($diff / 60) . ' 分钟前';
        }
        return $diff . ' 秒前';
    }
}