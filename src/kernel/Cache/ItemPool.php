<?php
/**
 * 缓存池
 * CacheItemPoolInterface 生成 CacheItemInterface 对象
 *
 * @author Jqh
 * @date   2017/6/16 17:32
 */

namespace Lxh\Cache;

use Lxh\Cache\Items\ArrayItem;
use Lxh\Cache\Items\StringItem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class ItemPool implements CacheItemPoolInterface
{
    /**
     * @var array
     */
    protected static $drivers = [];

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
     * 缓存驱动
     *
     * @var CacheInterface
     */
    protected $driver;

    /**
     * 默认缓存驱动
     *
     * @var string
     */
    protected $driverName = File::class;

    public function __construct($type = null)
    {
        $this->driverName = config('cache-driver', $this->driverName);

        $this->setDriver($this->createDriver($type));
    }

    /**
     * 获取缓存驱动
     *
     * @param  string $name
     * @return CacheInterface
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * @param CacheInterface $driver
     * @return $this
     */
    public function setDriver(CacheInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     *
     * @return CacheInterface
     */
    protected function createDriver($type = null)
    {
        $class = &$this->driverName;

        if (isset(static::$drivers[$class][$type])) {
            return static::$drivers[$class][$type];
        }

        return static::$drivers[$class][$type] = new $class($type);
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
            throw new InvalidArgumentException('Invalid cache item key.');
        }
        if (isset($this->pool[$key])) {
            return $this->pool[$key];
        }

        return $this->pool[$key] = $this->createItem($this->driver, $key);
    }

    /**
     *
     * @param $key
     * @return ArrayItem
     */
    public function getArrayItem($key)
    {
        if (! $this->validated($key)) {
            throw new InvalidArgumentException('Invalid cache item key.');
        }
        if (isset($this->pool[$key])) {
            return $this->pool[$key];
        }

        return $this->pool[$key] = $this->createArrayItem($this->driver, $key);
    }

    /**
     * @param $key
     * @param $value
     * @return StringItem
     */
    protected function createItem($key, $value)
    {
        return new StringItem($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return ArrayItem
     */
    protected function createArrayItem($key, $value)
    {
        return new ArrayItem($key, $value);
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
        foreach ($keys as &$k) {
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
            throw new InvalidArgumentException('Invalid cache item key.');
        }
        return isset($this->pool[$key]);
    }

    protected function validated(&$itemKey)
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
        $this->driver->flush();

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
            throw new InvalidArgumentException('Invalid cache item key.');
        }
        unset($this->pool[$key]);

        return $this->driver->delete($key);
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
        $key = $item->getKey();

        $result = true;
        // 缓存item
        if ($content = $item->hasNew()) {
            $result = $this->driver->set($key, $item->get());
        }

        // 设置缓存时间
        if ($at = $item->expiresAt()) {
            $this->driver->expiresAt($key, $at);
        }
        if ($after = $item->expiresAfter()) {
            $this->driver->expiresAfter($key, $after);
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
