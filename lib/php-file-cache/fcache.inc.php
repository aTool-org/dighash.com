<?php
/**
  * PHP File Cache, Used to instead of memcache in Host which can not install memcache.
  * 优点
  * 1.将每一个变量单独存放一个文件，可以有效减少磁盘读写数据，大大加快缓存存取速度；
  * 2.尽量减少对键值和缓存数据的计算，增加缓存过程的效率；
  *
  * 缺点
  * 1.弊端就是会产生很多的缓存文件。
  * 
  * 目前git上很大一部分缓存是写到一个缓存文件，意味着：
  * 1.无论你是读取多大的数据，都需要从磁盘读出整个文件到内存，然后解析，获取你要的部分数据；
  * 2.在缓存数据很大的时候，并不能起到缓存加速网站访问的目的，同时增加磁盘的读写负荷；
  * 3.在某一个临界点，可能会导致缓存性能比数据库还差；
  * 4.未经过严格测试，个人预估一般网站的数据都会达到100M以上，如果每次访问需要读取100M的数据，然后解析，性能非常低效。
  * @author: hustcc
  * @contract: http://50vip.com/
  * @data 2014-11-21
  */
class FCache {
    //Path to cache folder
    public $cache_path = 'tmp/';
    //Length of time to cache a file, default 1 day (in seconds)
    public $cache_time = 86400;
    //Cache file extension
    public $cache_extension = '.cache';
    
    /**
     * 构造函数
     */
    public function __construct($cache_path = 'tmp/', $cache_time = 86400, $cache_exttension = '.cache') {
        $this->cache_path = $cache_path;
        $this->cache_time = $cache_time;
        $this->cache_exttension = $cache_exttension;
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0777);
        }
    }
    
    //增加一对缓存数据
    public function add($key, $value) {
        $filename = $this->_get_cache_file($key);
        //写文件, 文件锁避免出错
        file_put_contents($filename, serialize($value), LOCK_EX);
    }
    
    //删除对应的一个缓存
    public function delete($key) {
        $filename = $this->_get_cache_file($key);
        unlink($filename);
    }
    
    //获取缓存
    public function get($key) {
        if ($this->_has_cache($key)) {
            $filename = $this->_get_cache_file($key);
            $value = file_get_contents($filename);
            if (empty($value)) {
                return false;
            }
            return unserialize($value);
        }
    }
    
    //删除所有缓存
    public function flush() {
        $fp = opendir($this->cache_path);
        while(!false == ($fn = readdir($fp))) {
            if($fn == '.' || $fn =='..') {
                continue;
            }
            unlink($this->cache_path . $fn);
        }
    }
    
    //是否存在缓存
    private function _has_cache($key) {
        $filename = $this->_get_cache_file($key);
        if(file_exists($filename) && (filemtime($filename) + $this->cache_time >= time())) {
            return true;
        }
        return false;
    }
    
    //验证cache key是否合法，可以自行增加规则
    private function _is_valid_key($key) {
        if ($key != null) {
            return true;
        }
        return false;
    }
    
    //私有方法
    private function _safe_filename($key) {
        if ($this->_is_valid_key($key)) {
            return md5($key);
        }
        //key不合法的时候，均使用默认文件'unvalid_cache_key'，不使用抛出异常，简化使用，增强容错性
        return 'unvalid_cache_key';
    }
    
    //拼接缓存路径
    private function _get_cache_file($key) {
        return $this->cache_path . $this->_safe_filename($key) . $this->cache_extension;
    }
}
?>