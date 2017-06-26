<?php
/**
 * 缓存池
 * CacheItemPoolInterface 生成 CacheItemInterface 对象
 *
 * @author Jqh
 * @date   2017/6/16 17:32
 */

namespace Lxh\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Lxh\Cache\Driver\Cache;

class ItemPool implements CacheItemPoolInterface
{
    /**
     * @var array
     */
    protected $pool = [];

    /**
     * 延迟缓存池
     *
     * @var array
     */
    protected $deferredPool = [];

    /**
     * 缓存驱动数组
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * 默认缓存驱动
     *
     * @var string
     */
    protected $driverName = 'File';

    public function __construct()
    {
        $this->driverName = config('cache-driver', $this->driverName);
            
        $this->drivers[$this->driverName] = $this->createDriver();
    }

    /**
     * 设置并获取缓存驱动
     *
     * @param  string $name
     * @return Cache
     */
    public function driver($name = null)
    {
        $this->driverName = $name ?: $this->driverName;

        if (isset($this->drivers[$name])) {
            return $this->drivers[$name];
        }
        return $this->drivers[$name] = $this->createDriver();
    }

    protected function createDriver()
    {
        $class = "Driver\\$this->driverName";
        return new $class();
    }

    /**
     * 返回「键」对应的一个缓存项。
     *
     * 此方法 **必须** 返回一个 CacheItemInterface 对象，即使是找不到对应的缓存项
     * 也 **一定不可** 返回 `null`。
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\Psr\Cache\InvalidArgumentException 异常会被抛出。
     *
     * @return CacheItemInterface
     *   对应的缓存项。
     */
    public function getItem($key)
    {
        if (! $this->validated($key)) {
            throw new \InvalidArgumentException('Invalid cache item key.');
        }
        if (isset($this->pool[$key])) {
            return $this->pool[$key];
        }

        return $this->pool[$key] = $this->createItem($key, $this->driver()->get($key));
    }

    /**
     * @return Item
     */
    public function createItem($key, $value)
    {
        return new Item($key, $value);
    }

    /**
     * 返回一个可供遍历的缓存项集合。
     *
     * @param array $keys
     *   由一个或者多个「键」组成的数组。
     *
     * @throws InvalidArgumentException
     *   如果 $keys 里面有哪个「键」不是合法，\Psr\Cache\InvalidArgumentException 异常
     *   会被抛出。
     *
     * @return array|\Traversable
     *   返回一个可供遍历的缓存项集合，集合里每个元素的标识符由「键」组成，即使即使是找不到对
     *   的缓存项，也要返回一个「CacheItemInterface」对象到对应的「键」中。
     *   如果传参的数组为空，也需要返回一个空的可遍历的集合。
     */
    public function getItems(array $keys = array())
    {
        $items = [];
        foreach ($keys as & $k) {
            $items[$k] = $this->getItem($k);
        }

        return $items;
    }

    /**
     * 检查缓存系统中是否有「键」对应的缓存项。
     *
     * 注意: 此方法应该调用 `CacheItemInterface::isHit()` 来做检查操作，而不是
     * `CacheItemInterface::get()`
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\Psr\Cache\InvalidArgumentException 异常会被抛出。
     *
     * @return bool
     *   如果存在「键」对应的缓存项即返回 true，否则 false
     */
    public function hasItem($key)
    {
        if (! $this->validated($key)) {
            throw new \InvalidArgumentException('Invalid cache item key.');
        }
        return isset($this->pool[$key]);
    }

    protected function validated(& $itemKey)
    {
        return preg_match('/[{}()\/\\@:]/', $itemKey);
    }

    /**
     * 清空缓冲池
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function clear()
    {
        $this->pool = [];
        return true;
    }

    /**
     * 从缓冲池里移除某个缓存项
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\Psr\Cache\InvalidArgumentException 异常会被抛出。
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function deleteItem($key)
    {
        if (! $this->validated($key)) {
            throw new \InvalidArgumentException('Invalid cache item key.');
        }
        unset($this->pool[$key]);
        return true;
    }

    /**
     * 从缓冲池里移除多个缓存项
     *
     * @param array $keys
     *   由一个或者多个「键」组成的数组。
     *
     * @throws InvalidArgumentException
     *   如果 $keys 里面有哪个「键」不是合法，\Psr\Cache\InvalidArgumentException 异常
     *   会被抛出。
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as & $k) {
            $this->deleteItem($k);
        }
        return true;
    }

    /**
     * 立刻为「CacheItemInterface」对象做数据持久化。
     *
     * @param CacheItemInterface $item
     *   将要被存储的缓存项
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function save(CacheItemInterface $item)
    {
        $driver = $this->driver();

        $key = $item->getKey();
        // 缓存item
        $result = $driver->save($key, $item->get());

        // 设置缓存时间
        if ($item->expiresAt) {
            $driver->expiresAt($key, $item->expiresAt);
        }
        if ($item->expiresAfter) {
            $driver->expiresAfter($key, $item->expiresAfter);
        }

        return $result;
    }

    /**
     * 稍后为「CacheItemInterface」对象做数据持久化。
     *
     * @param CacheItemInterface $item
     *   将要被存储的缓存项
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferredPool[] = $item;
        return true;
    }

    /**
     * 提交所有的正在队列里等待的请求到数据持久层，配合 `saveDeferred()` 使用
     *
     * @return bool
     *  成功返回 true，有错误发生返回 false
     */
    public function commit()
    {
        $result = true;
        foreach ($this->deferredPool as $item) {
            if (! $this->save($item)) {
                $result = false;
            }
        }

        return $result;
    }
}
