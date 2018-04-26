<?php

namespace Lxh\Cache;

use Lxh\Cache\Exceptions\InvalidArgumentException;
use Lxh\Cache\Exceptions\InvalidDriverException;

class Factory
{
    /**
     * @var array
     */
    protected static $channels = [];

    /**
     * @var array
     */
    protected static $dirversMap = [
        'file'     => File::class,
        'redis'    => Redis::class,
        'memcache' => Memcache::class,
    ];

    public function __construct()
    {
    }

    /**
     * 获取一个缓存通道
     *
     * @return CacheInterface
     */
    public function get($channel = 'primary')
    {
        return isset(static::$channels[$channel]) ? static::$channels[$channel] :
            (static::$channels[$channel] = $this->create($channel));
    }

    /**
     * 创建缓存通道
     *
     * @param string $channel
     * @return CacheInterface
     */
    public function create($channel)
    {
        if (!$channel) {
            throw new InvalidArgumentException('Invalid cache channel');
        }

        $options = $this->getOptions($channel);

        $cls = $options['driver'];

        $driver = new $cls($options);

        if (!$driver instanceof CacheInterface) {
            throw new InvalidDriverException('Invalid cache driver');
        }

        return $driver;
    }

    /**
     * @param string $channel
     * @return array
     */
    protected function getOptions($channel)
    {
        $options = (array)config('cache.'.$channel);
        if (empty($options['driver'])) {
            $options['driver'] = File::class;
        }

        if (isset(static::$dirversMap[$options['driver']])) {
            $options['driver'] = static::$dirversMap[$options['driver']];
        }

        if (empty($options['type'])) {
            $options['type'] = &$channel;
        }

        if (empty($options['use'])) {
            $options['use'] = true;
        }

        return $options;
    }
}
