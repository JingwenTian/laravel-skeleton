<?php
/**
 * Redis Client.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/30 14:46
 */

namespace App\Support\Queue;

/**
 * Class Redis.
 *
 * @package App\Support\Queue
 */
class Redis
{
    public static function prefix(): string
    {
        return config('cache.prefix').':';
    }

    /*
    |--------------------------------------------------------------------------
    | 基础操作
    |--------------------------------------------------------------------------
    |
    */

    /**
     * 设置缓存.
     *
     * @param string $key
     * @param $value
     * @param int $expire
     *
     * @return bool
     */
    public static function set(string $key = null, $value, int $expire = null)
    {
        if (empty($key) || empty($value)) {
            return false;
        }
        $key = self::prefix().$key;
        $result = app()->redis->set($key, \json_encode($value));
        if ($expire) {
            app()->redis->expire($key, $expire);
        }

        return $result;
    }

    /**
     * 获取缓存.
     *
     * @param string $key
     *
     * @return bool|mixed|null
     */
    public static function get(string $key = null)
    {
        if (empty($key)) {
            return false;
        }
        $key = self::prefix().$key;
        $data = app()->redis->get($key);
        if ($data) {
            return \json_decode($data, true);
        }

        return null;
    }

    /**
     * 缓存是否存在.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function exists(string $key = null): bool
    {
        if (empty($key)) {
            return false;
        }
        $key = self::prefix().$key;

        return (bool) app()->redis->exists($key);
    }

    /**
     * 删除缓存.
     *
     * @param string|array|null $key
     *
     * @return bool
     */
    public static function del($key = null): bool
    {
        if (empty($key)) {
            return false;
        }
        $key = self::prefix().$key;

        return (bool) app()->redis->del($key);
    }

    /**
     * 原子递增.
     *
     * @param string $key
     * @param int    $increment
     * @param int    $expire
     *
     * @return int
     */
    public static function incr(string $key = null, int $increment = null, int $expire = null): int
    {
        if (!$key) {
            return 0;
        }
        $key = self::prefix().$key;
        if (($increment ?? 1) > 1) {
            $result = app()->redis->incrby($key, $increment);
        } else {
            $result = app()->redis->incr($key);
        }
        if (($expire ?? 0) > 0) {
            app()->redis->expire($key, $expire);
        }

        return (int) $result;
    }

    /**
     * 原子递减.
     *
     * @param string $key
     * @param int    $increment
     * @param int    $expire
     *
     * @return int
     */
    public static function decr(string $key = null, int $increment = null, int $expire = null): int
    {
        if (!$key) {
            return 0;
        }
        $key = self::prefix().$key;
        if (($increment ?? 1) > 1) {
            $result = app()->redis->decrby($key, $increment);
        } else {
            $result = app()->redis->decr($key);
        }
        if (($expire ?? 0) > 0) {
            app()->redis->expire($key, $expire);
        }

        return (int) $result;
    }

    /**
     * 获取通配符规则下的所有 key.
     *
     * @param string $pattern
     *
     * @return mixed
     */
    public static function keys(string $pattern)
    {
        return app()->redis->keys(self::prefix().$pattern);
    }

    /**
     * 批量删除多个缓存 key.
     *
     * @param string $pattern
     *
     * @return mixed
     */
    public static function missing(string $pattern)
    {
        $keys = app()->redis->keys(self::prefix().$pattern);
        if ($keys) {
            app()->redis->del($keys);
        }

        return $keys;
    }

    /*
    |--------------------------------------------------------------------------
    | 队列操作
    |--------------------------------------------------------------------------
    |
    */

    /**
     * 入队列.
     *
     * @param string $key
     * @param array  $value
     *
     * @return int
     */
    public static function push(string $key, array $value): int
    {
        return app()->redis->lpush($key, \json_encode($value));
    }

    /**
     * 出队列.
     *
     * @param string $key
     * @param bool   $block   是否阻塞式弹出
     * @param int    $timeout 阻塞时长
     *
     * @return array
     */
    public static function pop(string $key, bool $block = null, int $timeout = null): array
    {
        if ($block ?? true) {
            $value = app()->redis->brpop($key, $timeout ?? 20);
            if (!empty($value) && !empty($value[1])) {
                $data = \json_decode($value[1], true);
                if (\json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }
        } else {
            if (static::len($key)) {
                $value = \json_decode(app()->redis->rpop($key), true);
                if (\json_last_error() === JSON_ERROR_NONE) {
                    return $value;
                }
            }
        }

        return [];
    }

    /**
     * 队列长度.
     *
     * @param string $key
     *
     * @return int
     */
    public static function len(string $key): int
    {
        return app()->redis->llen($key);
    }
}
