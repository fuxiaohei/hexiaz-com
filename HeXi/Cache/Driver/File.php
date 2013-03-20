<?php

/**
 * 缓存文件类
 * Class FileCache
 */
class FileCache extends CacheDriver {

    /**
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire) {
        $file = $this->options['path'] . md5($key) . '.cache';
        #保存过期时间和序列化的数据
        $data = array('expire' => time() + $expire, 'data' => $value);
        $data = serialize($data);
        if ($this->options['compress']) {
            #启用压缩
            $data = gzcompress($data);
        }
        file_put_contents($file, $data);
        return true;
    }

    /**
     * 获取缓存
     * 没有是为null
     * @param string $key
     * @param bool $ignoreExpire
     * @return null|mixed
     */
    public function get($key, $ignoreExpire = false) {
        $file = $this->options['path'] . md5($key) . '.cache';
        if (!is_file($file)) {
            return null;
        }
        $data = file_get_contents($file);
        if ($this->options['compress']) {
            $data = gzuncompress($data);
        }
        $data = unserialize($data);
        #忽略过期情况
        if ($ignoreExpire) {
            return $data['data'];
        }
        $expire = $data['expire'];
        if ($expire < time()) {
            return null;
        }
        return $data['data'];
    }

    /**
     * 删除缓存
     * 没有时为null
     * @param string $key
     * @return bool|null
     */
    public function rm($key) {
        $file = $this->options['path'] . md5($key) . '.cache';
        if (is_file($file)) {
            return unlink($file);
        }
        return null;
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear() {
        $files = glob($this->options['path'] . '*.cache');
        foreach ($files as $f) {
            unlink($f);
        }
        return true;
    }

    /**
     * 回收缓存
     */
    protected function gc() {
        #关闭掉回收功能
        if(!$this->options['gc']){
            return;
        }
        #按照概率计算回收
        if (rand(0, $this->options['gc']) <= 1) {
            $this->clear();
        }
    }


}