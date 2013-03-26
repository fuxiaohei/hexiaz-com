<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-25
 * Time: 下午8:32
 * To change this template use File | Settings | File Templates.
 */

class userModel extends Model {

    protected $db;

    public function __construct() {
        $this->db = Db::connect();
    }

    public function getToken($name, $password) {
        $sql = $this->sql('hx_user', 'user_id,user_name,password,user_nickname,create_time,user_email,user_url,user_avatar')
            ->where('user_name = :name')
            ->where('user_role = "admin"')
            ->where('active_status = "active"')
            ->select();
        $userData = $this->db->query($sql, array('name' => $name));
        if (!$userData) {
            return false;
        }
        $password = sha1($userData->user_id . '-' . md5($password) . '-' . $name);
        if ($password != $userData->password) {
            return false;
        }
        unset($userData->password);
        $userData->token = $this->createToken($userData->user_id);
        return $userData;
    }

    public function checkToken($user, $token) {
        $sql = $this->sql('hx_token', 'expire_time')
            ->where('user_id = :id')
            ->where('token_value = :token')
            ->select();
        $token = $this->db->query($sql, array(
            'id' => $user,
            'token' => $token
        ));
        if (!$token) {
            return false;
        }
        if ($token->expire_time > time()) {
            return true;
        }
        return false;
    }

    private function createToken($user) {
        $expire = time() + 30 * 3600 * 24;
        $token = sha1($user . '-' . $expire);
        $sql = $this->sql('hx_token', 'user_id,token_value,create_time,expire_time,token_ip,token_user_agent')->insert();
        $this->db->exec($sql, array(
            'user_id' => $user,
            'token_value' => $token,
            'create_time' => time(),
            'expire_time' => $expire,
            'token_ip' => Request::ip(),
            'token_user_agent' => Request::userAgent()
        ));
        return $token;
    }
}