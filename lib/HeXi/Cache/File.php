<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-14
 * Time: 下午7:38
 * To change this template use File | Settings | File Templates.
 */
class FileCache extends AbstractCache {

    protected $path;

    protected $expire;

    protected function init() {
        $this->path   = HeXi::$path . $this->config['path'];
        $this->expire = (int)$this->config['expire'];
    }

    public function put($key, $value) {
        $file = $this->path . md5($key) . '.tmp';
        file_put_contents($file, $value);
    }

    public function get($key, $default = null) {
        $file = $this->path . md5($key) . '.tmp';
        if (!is_file($file)) {
            return $default;
        }
        if (filemtime($file) + $this->expire < NOW) {
            return $default;
        }
        return file_get_contents($file);
    }

    public function delete($key) {
        $file = $this->path . md5($key) . '.tmp';
        if (!is_file($file)) {
            return true;
        }
        unlink($file);
        return true;
    }

    public function expired($key, $default) {
        $file = $this->path . md5($key) . '.tmp';
        if (!is_file($file)) {
            return $default;
        }
        return file_get_contents($file);
    }

    public function clear() {
        $files = glob($this->path . '*.tmp');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }

    public function clean() {
        $files = glob($this->path . '*.tmp');
        foreach ($files as $file) {
            if (filemtime($file) + $this->expire < NOW) {
                unlink($file);
            }
        }
        return true;
    }


}
