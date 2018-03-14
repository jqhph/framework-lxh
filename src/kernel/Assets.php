<?php

namespace Lxh;

use Lxh\Helper\Util;

class Assets
{
    protected static $configs = [];

    /**
     * @return array
     */
    public static function loaderConfig()
    {
        if (static::$configs) {
            return static::$configs;
        }

        $syst = self::config();
        $conf = config('client.loader');

        return static::$configs = Util::merge($syst, $conf, true);
    }

    /**
     * seajs配置定义
     *
     * @return array
     */
    public static function config()
    {
        $server = config('client.resource-server');
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


        return [
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
