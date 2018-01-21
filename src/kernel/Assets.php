<?php

namespace Lxh;

class Assets
{
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
            $module = lc_dash($module);
            $paths['@' . $module] = "$server/assets/{$version}/$module";

        }

        // 系统预定义资源路径
        $paths['@lxh'] = "$server/assets/admin";
        $paths['@plugins'] = "$server/assets/plugins";


        return [
            // 设置路径，方便跨目录调用
            'paths' => &$paths,
//            'alias' => [
//            ]
        ];
    }

    public static function publics()
    {
        return [
            'public-js'=> [
                '@lxh/js/container.min'
            ],
            'public-css' => [

            ],
        ];
    }

}
