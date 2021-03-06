<?php
namespace AppModule\Cache;

use Core\Database\CacheInterface;
use Predis\Client;

class RedisCache implements CacheInterface
{
    private $redis;

    /**
     * RedisCache constructor.
     */
    public function __construct()
    {
        $file = __DIR__ . '/../../../config/my_setting.ini';
        if (!$settings = parse_ini_file($file, TRUE)) throw new \Exception('Unable to open ' . $file . '.');

        $this->redis = new Client(array('host' => $settings['cache']['host']));
    }

    /**
     * @param $keys
     * @return string
     */
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

    /**
     * @param $key
     * @return string
     */
    public function hashKey($key)
    {
        if(is_object($key)) {
            $id = $key->id ?? '1';
            return get_class($key). '_' . $id . '_' . strtotime($key->updated_at);
        } else {
            return $key;
        }
    }

    /**
     * Cache into redis instance with the key associte with it if the key exist it return the cache else this will
     * be cached into redis.
     *
     * @param $keys
     * @param callable $callback
     * @return mixed|string
     */
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

    /**
     * @param $key
     */
    public function del($key)
    {
        $key = $this->hashKeys($key);

        $this->redis->del($key);
    }
}