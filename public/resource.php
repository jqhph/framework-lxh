<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/5
 * Time: 6:48
 */

$loader = new ResourceLoader();

$f = !empty($_GET['f']) ? $_GET['f'] : '';
$t = !empty($_GET['t']) ? $_GET['t'] : 'js';

$loader->filename     = $f;
$loader->resourceType = $t;

$loader->handle();
//$lastmodified = max($lastmodified, filemtime($path));
// 输出结果
echo $loader->fetch();

class ResourceLoader
{
    /**
     * 静态资源文件名
     *
     * @var array
     */
    public $filenames;

    /**
     * 静态资源类型
     * @var string
     */
    public $resourceType = 'js';

    /**
     * 压缩编码类型
     * @var string
     */
    public $encoding = 'gzip';

    /**
     * 允许加载的资源类型
     *
     * @var array
     */
    public $allowTypes = ['js', 'css', 'json'];

    /**
     * @var Lxh\Config\Config
     */
    protected $config;

    public function __construct()
    {
        define('__ROOT__', dirname(__DIR__) . '/src/');
        require __ROOT__ . 'config/ini.php';
    }

    public function config()
    {
        if ($this->config) {
            return $this->config;
        }
        require __ROOT__ . 'kernel/Helper/Entity.php';
        require __ROOT__ . 'kernel/Config/Config.php';

        return $this->config = new \Lxh\Config\Config();
    }

    public function handle()
    {
        if (! $this->filenames) {
            return $this->notFound();
        }
        // 类型检测
        if (! in_array($this->resourceType, $this->allowTypes)) {
            return $this->badRequest();
        }

    }

    public function fetch()
    {
        return $this->filename;
    }

    public function badRequest()
    {
        header('HTTP/1.0 503 Not Implemented');
    }

    public function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
    }

    public function notFound()
    {
        header('HTTP/1.0 404 Not Found');
    }
}