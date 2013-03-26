<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-24
 * Time: 下午10:59
 * To change this template use File | Settings | File Templates.
 */

class indexController extends Controller {

    public function indexAction() {
        Response::body('API is running!');
    }

    public function authorizeAction() {
        if (!$this->is('post')) {
            Response::status(401);
            return;
        }
        if (Input::get('check')) {
            $this->json(array('auth' => Model::exec('user', 'checkToken', array(Input::get('user'), Input::get('token')))));
            return;
        }
        $res = Model::exec('user', 'getToken', array(Input::get('user'), Input::get('password')));
        if ($res === false) {
            $this->json(array('auth' => false));
        } else {
            $this->json(array('auth' => true, 'user' => $res));
        }
    }

    public function preAction(){
        $data = Model::exec('crx','preData');
        $data['nodeCount'] = count($data['node']);
        $data['commentCount'] = count($data['comment']);
        $this->json($data);
    }
}