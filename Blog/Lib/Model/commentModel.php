<?php

/**
 * 评论操作类
 * Class commentModel
 */
class commentModel extends Model {

    /**
     * 数据库连接
     * @var DbDriver|PDODriver
     */
    protected $db;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->db = Db::connect();
    }

    /**
     * 添加评论
     * @param string $author
     * @param string $email
     * @param string $url
     * @param string $content
     * @param int $node
     * @param int $parent
     * @return bool|commentStore
     */
    public function addOne($author, $email, $url, $content, $node, $parent) {
        $col = 'author,author_email,author_url,content,create_time,node_id,user_id,status,comment_type';
        $col .= ',parent_id,user_ip,user_agent';
        $sql = $this->sql('hx_comment', $col)
            ->insert();
        $status = $this->checkEmail($email);
        $this->db->exec($sql, array(
            'author' => $author,
            'author_email' => $email,
            'author_url' => $url,
            'content' => $content,
            'create_time' => time(),
            'node_id' => $node,
            'user_id' => 0,
            'status' => $status,
            'comment_type' => 'comment',
            'parent_id' => $parent,
            'user_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ));
        $id = $this->db->lastId();
        #更新评论统计
        if ($status == 'public' && $parent < 1) {
            $this->updateCommentCount($node);
        }
        return $this->getOne('comment_id', $id);
    }


    /**
     * 更新评论统计
     * @param int $node
     */
    public function updateCommentCount($node) {
        $sql = 'UPDATE hx_node SET comment_count = ( SELECT count(*) FROM hx_comment WHERE parent_id = 0 AND node_id = ' . $node . ' AND status = "public" AND comment_type = "comment" ) WHERE node_id = ' . $node;
        $this->db->exec($sql);
    }

    /**
     * 从email判断评论状态
     * @param string $email
     * @return string
     */
    private function checkEmail($email) {
        $sql = $this->sql('hx_comment')
            ->where('status = "public"')
            ->where('comment_type = "comment"')
            ->where('author_email = :email')
            ->count();
        $count = $this->db->query($sql, array('email' => $email));
        if ($count->num > 0) {
            return 'public';
        }
        //return 'check';
        return 'public';
    }

    /**
     * 获取一个评论
     * @param string $key
     * @param mixed $value
     * @return bool|commentStore
     */
    public function getOne($key, $value) {
        $col = 'author,author_email,author_url,content,create_time,node_id,user_id,status,comment_type';
        $col .= ',parent_id,user_ip,user_agent';
        $sql = $this->sql('hx_comment', $col)
            ->where($key . ' = :' . $key)
            ->select();
        return $this->db->query($sql, array($key => $value), 'comment');
    }

    /**
     * 获取评论树
     * @param int $node
     * @return array
     */
    public function getTree($node) {
        $col = 'comment_id,author,author_email,author_url,content,create_time,node_id,user_id,status,comment_type';
        $col .= ',parent_id,user_ip,user_agent';
        $sql = $this->sql('hx_comment', $col)
            ->where('status = "public"')
            ->where('comment_type = "comment"')
            ->where('node_id = :node_id')
            ->select();
        $commentData = $this->db->queryAll($sql, array('node_id' => $node), 'comment');
        if ($commentData) {
            $tempData = array();
            foreach ($commentData as $c) {
                $tempData[$c->comment_id] = $c;
            }
            $commentData = array();
            foreach ($tempData as $k => $c) {
                if ($tempData[$c->parent_id]) {
                    #将子评论移动到父评论麾下
                    $tempData[$c->parent_id]->children[] =& $tempData[$k];
                    continue;
                }
                if ($c->parent_id > 0) {
                    continue;
                }
                $commentData[] =& $tempData[$k];
            }
        }
        return $commentData;
    }

    /**
     * 获取最新评论
     * @param int $size
     * @return array
     */
    public function getPop($size = 2) {
        $sql = $this->sql('hx_comment', 'comment_id,author,content,node_key')
            ->leftJoin('hx_node', 'hx_node.node_id = hx_comment.node_id')
            ->limit($size)
            ->order('hx_comment.create_time DESC')
            ->where('hx_comment.status = "public"')
            ->where('comment_type = "comment"')
            ->select();
        return $this->db->queryAll($sql, null, 'comment');
    }
}