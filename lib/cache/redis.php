<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2017/2/13
 * Time: 11:26
 * QQ: 84855512
 */

namespace lib\cache;

use lib\cache;

class redis extends cache
{
    protected $expire;
    protected $client;

    public function __construct()
    {
        if (!$this->client) {
            $this->expire = config('app.cache_expire');
            $name = !empty(config('app.cache_redis_dir')) ? config('app.cache_redis_dir') : 'default';
            $this->client = \lib\redis::_instance($name);
        }
    }

    public function set($key, $value, $expire = null)
    {
        if ($expire === null) {
            $expire = $this->expire;
        }
        $key = $this->getCacheKey($key);
        if (!is_numeric($value)) {
            $value = serialize($value);
        }
//        if (function_exists('gzcompress')) {
//            $value = gzcompress($value);
//        }
        if ($this->client->set($key, $value)) {
            if ($expire > 0) {
                $this->client->expire($key, $expire);
            }
            return true;
        }
        return false;
    }

    public function isExists($key)
    {
        $key = $this->getCacheKey($key);
        if ($this->client->keys($key)) {
            return true;
        }
        return false;
    }

    protected function getCacheKey($key)
    {
        return $key;
    }

    public function get($key)
    {
        $key = $this->getCacheKey($key);
        $value = $this->client->get($key);
        if ($value && !is_numeric($value)) {
            $value = unserialize($value);
        }
//        if (function_exists('gzcompress')) {
//            $value = gzuncompress($value);
//        }
        return $value;
    }

    public function del($key)
    {
        $this->client->del($this->getCacheKey($key));
    }
}