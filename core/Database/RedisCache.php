<?php
namespace Core\Database;

use Predis\Client;

class RedisCache {
    private $redis;

    public function __construct()
    {
        $this->redis = new Client(array('host' => 'redis'));
    }

    public function hashKeys($keys)
    {
        if(is_array($keys)) {
            $key = [];

            foreach ($keys as $k) {
                array_push($key, $this->hashKey($k));
            }
            return implode('-', $key);

        } else {
            return $this->hashKey($keys);
        }
    }
    public function hashKey($key)
    {
        if(is_object($key)) {
            return get_class($key). '_' . $key->id . '_' . strtotime($key->updated_at);
        } else {
            return $key;
        }
    }

    public function cache($keys, Callable $callback)
    {
        $key = $this->hashKeys($keys);

        if($value = $this->redis->get($key)) {
            return unserialize($this->redis->get($key));
        }

        $value = $callback();
        $this->redis->setex($key,  60 * 60 * 96,serialize($value));

        return $value;
    }

}