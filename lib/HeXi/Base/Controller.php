<?php
/**
 *
 *
 * @property Input    input
 * @property Request  request
 * @property Response response
 * @property Cookie   cookie
 * @property Session  session
 * @property View     view
 */
abstract class BaseController extends BaseClass {

    public function __get($key) {
        switch ($key) {
            case 'input':
                $this->input = Input::instance();
                return $this->input;
            case 'request':
                $this->request = Request::instance();
                return $this->request;
            case 'response':
                $this->response = Response::instance();
                return $this->response;
            case 'cookie':
                $this->cookie = Cookie::instance();
                return $this->cookie;
            case 'session':
                $this->session = Session::instance();
                return $this->session;
        }
        return null;
    }

    protected function view($dir = false, $engine = false) {
        if (!$this->view) {
            $this->view = View::make($dir, $engine);
        }
        return $this->view;
    }

    protected function model($command, $args = array()) {
        if (!strstr($command, '->')) {
            $modelName = $command . 'Model';
            return $this->getModel($modelName);
        }
        $command   = explode('->', $command);
        $modelName = $command[0] . 'Model';
        $model     = $this->getModel($modelName);
        if (!$model) {
            return null;
        }
        if (!is_callable(array($model, $command[1]))) {
            return null;
        }
        return call_user_func_array(array($model, $command[1]), $args);
    }

    private function getModel($modelName) {
        if (!$GLOBALS['model'][$modelName]) {
            if (!import(APP_NAME . '.Model.' . $modelName)) {
                return null;
            }
            $GLOBALS['model'][$modelName] = new $modelName();
        }
        return $GLOBALS['model'][$modelName];
    }
}
