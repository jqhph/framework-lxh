<?php
/**
 * 文件缓存
 *
 * @author Jqh
 * @date   2017/7/27 09:21
 */
namespace Lxh\Kernel\Cache;

use Lxh\File\FileManager;

class File extends Cache
{
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

    protected $defaultType = 'default';

    /**
     * File constructor.
     * @param string $name 文件夹名，默认为空
     */
    public function __construct($name = '')
    {
        $this->type = $name;

        $this->root = __DATA_ROOT__ . 'file-cache/';

        $this->file = files();
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
     * 设置缓存目录
     *
     * @param  string $dirname 目录名
     * @return static
     */
    public function setType($dirname)
    {
        if (empty($dirname)) {
            return $this;
        }
        $this->type = $dirname;
        return $this;
    }

    public function getType()
    {
        return $this->type ?: $this->defaultType;
    }

    public function getTypePath($name)
    {
        return $this->getBasePath() . $name;
    }

    /**
     * 移除缓存目录类型下的所有缓存
     *
     * @param  string $dirname 目录名，传空则清除默认目录
     * @return bool
     */
    public function removeType($dirname = '')
    {
        $dirname = $dirname ?: $this->defaultType;

        return $this->file->removeInDir($this->getBasePath() . $dirname);
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
        return USE_CACHE;
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

        $data = $this->file->getPhpContents($this->normalizePath($key));
        if (empty($data['value'])) {
            return false;
        }

        // 判断是否过期
        if ($this->isTimeout($key, $data)) {
            // 如过期则删除缓存文件
            $this->delete($key);
            return false;
        }

        return $data['value'];
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
    protected function normalizeValue(& $value, $timeout = 0)
    {
        $time = time() + $timeout;
        if ($timeout == 0) {
            $time = 0;
        }

        $value = [
            'value' => $value,
            'timeout' => $time
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
