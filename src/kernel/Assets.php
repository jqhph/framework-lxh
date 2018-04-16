<?php

namespace Lxh;

use Lxh\Helper\Util;

class Assets
{
    protected static $configs = [];
    protected static $aliasConfig = [];

    /**
     * @return array
     */
    public static function loaderConfig()
    {
        if (static::$configs) {
            return static::$configs;
        }

        $syst = self::aliasConfig();
        $conf = (array)config('client.loader');

        return static::$configs = Util::merge($syst, $conf, true);
    }

    /**
     * 解析静态资源路径
     *
     * @param $path
     * @return string
     */
    public static function parse($path)
    {
        $config = static::aliasConfig();

        return static::parsePath(
            static::parseAlias($path, $config), $config
        );
    }

    public static function parseCss($path)
    {
        $path = static::parse($path);

        if (strpos($path, '.css') === false) {
            $path .= '.css';
        }
        if (strpos($path, '.?') === false) {
            $path .= '?v='.$GLOBALS['css-version'];
        }

        return $path;
    }

    /**
     * 别名解析
     *
     * @param $path
     * @return string
     */
    protected static function parseAlias($path, array &$config)
    {
        $path = explode('?', $path);

        if (!empty($config['alias'][$path[0]])) {
            $path[0] = $config['alias'][$path[0]];
        }
        return join('?', $path);
    }

    /**
     * 路径前缀解析
     *
     * @param $path
     * @return string
     */
    protected static function parsePath($path, array &$config)
    {
        $path = explode('/', $path);

        if (!empty($config['paths'][$path[0]])) {
            $path[0] = $config['paths'][$path[0]];
        }
        return join('/', $path);
    }

    /**
     * seajs配置定义
     *
     * @return array
     */
    protected static function aliasConfig()
    {
        if (static::$aliasConfig) {
            return static::$aliasConfig;
        }

        $server  = config('client.resource-server');
        $version = config('client.resource-version', 'primary');

        $paths = [];

        // 用户自定义js基本路径
        foreach (config('modules') as &$module) {
            $module = slug($module);
            $paths['@' . $module] = "$server/assets/{$version}/$module";

        }

        // 系统预定义资源路径
        $paths['@lxh'] = "$server/assets/admin";
        $paths['@plugins'] = "$server/assets/plugins";


        return static::$aliasConfig = [
            // 设置路径，方便跨目录调用
            'paths' => &$paths,
            'alias' => [
                'jquery' => '@lxh/js/jquery.min',
            ]
        ];
    }

    public static function publics()
    {
        return [
            'public-js'=> [
            ],
            'public-css' => [

            ],
        ];
    }

}
