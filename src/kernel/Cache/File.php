<?php
/**
 * 文件缓存
 *
 * @author Jqh
 * @date   2017/7/27 09:21
 */
namespace Lxh\Cache;

use Lxh\Cache\Exceptions\InvalidArgumentException;
use Lxh\File\FileManager;

class File implements CacheInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * 缓存根目录
     *
     * @var string
     */
    protected $root;

    /**
     * @var FileManager
     */
    protected $file;

    /**
     * 设置缓存目录
     *
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $defaultType = 'default';

    /**
     * File constructor.
     * @param string $name 文件夹名，默认为空
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->options = &$options;

            $this->type = getvalue($options, 'type');
            $this->root = getvalue($options, 'path', __DATA_ROOT__ . 'file-cache/');
        } else {
            $this->type = $options ?: $this->defaultType;
            $this->root = __DATA_ROOT__ . 'file-cache/';
        }

        $this->file = files();
    }

    /**
     *
     * @param string $type 缓存目录
     * @return static
     */
    public static function create($type = null)
    {
        return new static($type);
    }

    /**
     * 保存缓存
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $timeout 设置缓存$timeout秒后过期，0为不过期
     * @return bool
     */
    public function set($key, $value, $timeout = 0)
    {
        if (empty($key)) {
            return false;
        }

        $this->normalizeValue($value, $timeout);

        return $this->file->putPhpContents($this->normalizePath($key), $value);
    }

    /**
     * 设置数组缓存
     *
     * @param $key
     * @param array $value
     * @param int $timeout
     * @return bool
     */
    public function setArray($key, array $value, $timeout = 0)
    {
        return $this->set($key, $value, $timeout);
    }

    /**
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function appendInArray($key, $value, $timeout = 0)
    {
        if (empty($key)) return false;

        if (is_array($value)) {
            throw new InvalidArgumentException;
        }

        $content = $this->getArray($key);

        $content[] = &$value;

        return $this->setArray($key, $content, $timeout);
    }

    /**
     * 从数组中删除一个值
     *
     * @param string $key
     * @param $value
     * @return bool
     */
    public function deleteInArray($key, $value, $timeout = 0)
    {
        if (empty($key)) return false;

        $content = $this->getArray($key);

        if ($content === false) {
            return true;
        }

        $isInt = is_integer($value);

        foreach ($content as $k => &$item) {
            if ($isInt) {
                if ($value === (int) $item) {
                    unset($content[$k]);
                }
                continue;
            }
            if ($item == $value) {
                unset($content[$k]);
            }
        }

        return $this->setArray($key, $content, $timeout);
    }

    /**
     * 判断缓存是否存在
     *
     * @param $key
     * @return bool
     */
    public function exist($key)
    {
        if (! $this->useCache() || empty($key)) {
            return false;
        }

        $content = $this->file->getPhpContents($this->normalizePath($key));

        return $this->isEffective($key, $content);

    }

    /**
     * 判断缓存内容是否有效
     *
     * @param $key
     * @param $data
     * @return bool
     */
    protected function isEffective($key, &$content)
    {
        if (!isset($content['value']) || $content['value'] === '' || $content['value'] === false) {
            return false;
        }

        // 判断是否过期
        if ($this->isTimeout($key, $content)) {
            // 如过期则删除缓存文件
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * 设置缓存目录
     *
     * @param  string $type 目录名
     * @return static
     */
    public function setType($type)
    {
        if (empty($type)) {
            return $this;
        }
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type ?: $this->defaultType;
    }

    /**
     * 获取缓存文件目录
     *
     * @param null $name
     * @return string
     */
    public function getTypePath($name = null)
    {
        return $this->getBasePath() . ($name ?: $this->getType());
    }

    /**
     * 移除缓存目录类型下的所有缓存
     *
     * @param  string $type 目录名，传空则清除默认目录
     * @return bool
     */
    public function flush($type = null)
    {
        return $this->file->removeInDir($this->getBasePath() . ($type ?: $this->getType()));
    }

    public function reset()
    {
        return $this;
    }

    /**
     * 是否使用缓存
     * 是返回true，否则返回false
     *
     * @return bool
     */
    public function useCache()
    {
        return getvalue($this->options, 'use', true);
    }

    /**
     * 获取缓存内容
     *
     * @param  string $key
     * @return mixed 内容过期或不存在返回false
     */
    public function get($key)
    {
        if (! $this->useCache() || empty($key)) {
            return false;
        }

        $content = $this->file->getPhpContents($this->normalizePath($key));
        if (! $this->isEffective($key, $content)) {
            return false;
        }

        return $content['value'];
    }

    /**
     * 获取数组缓存
     *
     * @param string $key
     * @return array 内容过期或不存在返回false
     */
    public function getArray($key)
    {
        $content = $this->get($key);

        if ($content === false) {
            return false;
        }

        if (! is_array($content)) {
            return (array)$content;
        }
        return $content;
    }

    /**
     * 自增1
     *
     * @param $key
     * @param int $timeout
     * @return mixed
     */
    public function incr($key, $timeout = 0)
    {
        $value = $this->get($key);

        return $this->set($key, $value++, $timeout);
    }

    /**
     * 自减1
     *
     * @param $key
     * @param int $timeout
     * @return mixed
     */
    public function decr($key, $timeout = 0)
    {
        $value = $this->get($key);

        return $this->set($key, $value--, $timeout);
    }

    /**
     * 判断缓存内容是否过期
     *
     * @param  string $key
     * @param  array  $data 缓存内容
     * @return bool 是返回true，否返回false
     */
    protected function isTimeout($key, array & $data)
    {
        if (empty($data['timeout'])) {
            return false;
        }

        if (time() > $data['timeout']) {
            return true;
        }

        return false;
    }

    /**
     * 格式化value
     *
     * @param  mixed $value
     * @return array
     */
    protected function normalizeValue(&$value, $timeout = 0)
    {
        $time = time() + $timeout;
        if ($timeout == 0) {
            $time = 0;
        }

        $value = [
            'value' => is_bool($value) ? (int) $value : $value,
            'timeout' => &$time
        ];
    }

    /**
     * 删除缓存
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        if (empty($key)) {
            return false;
        }
        return $this->file->removeFile($this->normalizePath($key));
    }

    /**
     * 获取缓存路径
     *
     * @param  string $Key
     * @return string
     */
    public function normalizePath($key)
    {
        $type = $this->getType();

        return "{$this->root}{$type}/{$key}";
    }

    /**
     * 获取缓存文件修改时间
     *
     * @param  string $k
     * @return int
     */
    public function filemtime($k)
    {
        $path = $this->normalizePath($k);

        return is_file($path) ? filemtime($path) : 0;
    }

    /**
     * 设置缓存过期时间
     *
     * @param  string $key
     * @param  int    $expires 设置缓存在某一时间点过期，用时间戳格式，0为不过期
     * @return bool
     */
    public function expiresAt($key, $expires)
    {
        if (empty($key)) {
            return false;
        }

        $path = $this->normalizePath($key);

        $data = $this->file->getPhpContents($path);
        if (empty($data)) {
            return false;
        }
        $data['timeout'] = (int) $expires;

        return $this->file->putPhpContents($path, $data);
    }

    /**
     * 设置缓存n秒后过期
     *
     * @param  string $key
     * @param  int    $timeout 设置缓存在$timeout秒后过期
     * @return bool
     */
    public function expiresAfter($key, $timeout)
    {
        if (empty($key)) {
            return false;
        }

        $path = $this->normalizePath($key);

        $data = $this->file->getPhpContents($path);
        if (empty($data)) {
            return false;
        }
        $data['timeout'] = time() + ((int) $timeout);

        return $this->file->putPhpContents($path, $data);
    }

    public function setBasePath($path)
    {
        $this->root = $path;

        return $this;
    }

    /**
     * 获取缓存根目录
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->root;
    }

}
