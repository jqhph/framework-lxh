<?php
/**
 * 缓存项
 *
 * PSR6
 *
 * @author Jqh
 * @date   2017/6/16 17:24
 */

namespace Lxh\Cache;

use Psr\Cache\CacheItemInterface;

class Item implements CacheItemInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * 过期的精确时间
     *
     * @var string
     */
    public $expiresAt;

    /**
     * 设置缓存项的过期时间，单位秒
     *
     * @var int
     */
    public $expiresAfter;

    /**
     * @var bool
     */
    protected $isHit = false;

    public function __construct($key, $content = null)
    {
        $this->key = $key;

        $this->content = & $content;

        $this->isHit = $this->content ? true : false;
    }

    /**
     * 返回当前缓存项的「键」
     *
     * 「键」由实现类库来加载，并且高层的调用者（如：CacheItemPoolInterface）
     *  **应该** 能使用此方法来获取到「键」的信息。
     *
     * @return string
     *   当前缓存项的「键」
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * 凭借此缓存项的「键」从缓存系统里面取出缓存项。
     *
     * 取出的数据 **必须** 跟使用 `set()` 存进去的数据是一模一样的。
     *
     * 如果 `isHit()` 返回 false 的话，此方法必须返回 `null`，需要注意的是 `null`
     * 本来就是一个合法的缓存数据，所以你 **应该** 使用 `isHit()` 方法来辨别到底是
     * "返回 null 数据" 还是 "缓存里没有此数据"。
     *
     * @return mixed
     *   此缓存项的「键」对应的「值」，如果找不到的话，返回 `null`
     */
    public function get()
    {
        if ($this->isHit() === false) {
            return null;
        }

        return $this->content;
    }

    /**
     * 确认缓存项的检查是否命中。
     *
     * 注意: 调用此方法和调用 `get()` 时 **一定不可** 有先后顺序之分。
     *
     * @return bool
     *   如果缓冲池里有命中的话，返回 `true`，反之返回 `false`
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * 为此缓存项设置「值」。
     *
     * 参数 $value 可以是所有能被 PHP 序列化的数据，序列化的逻辑
     * 需要在实现类库里书写。
     *
     * @param mixed $value
     *   将被存储的可序列化的数据。
     *
     * @return static
     *   返回当前对象。
     */
    public function set($value)
    {
        $this->content = $value;

        $this->isHit = true;
    }

    /**
     * 设置缓存项的准确过期时间点。
     *
     * @param \DateTimeInterface $expiresAt
     *
     *   过期的准确时间点，过了这个时间点后，缓存项就 **必须** 被认为是过期了的。
     *   如果明确的传参 `null` 的话，**可以** 使用一个默认的时间。
     *   如果没有设置的话，缓存 **应该** 存储到底层实现的最大允许时间。
     *
     * @return static
     *   返回当前对象。
     */
    public function expiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * 设置缓存项的过期时间。
     *
     * @param int|\DateInterval $time
     *   以秒为单位的过期时长，过了这段时间后，缓存项就 **必须** 被认为是过期了的。
     *   如果明确的传参 `null` 的话，**可以** 使用一个默认的时间。
     *   如果没有设置的话，缓存 **应该** 存储到底层实现的最大允许时间。
     *
     * @return static
     *   返回当前对象
     */
    public function expiresAfter($time)
    {
        $this->expiresAfter = $time;
        return $this;
    }

}
