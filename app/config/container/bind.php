<?php
/**
 * 服务注册文件
 *
 * @author Jqh
 * @date   2017/6/13 20:15
 */

use Lxh\Config\Config;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Arr;
use Lxh\Router\Dispatcher;

$container = container();

$container->singleton('router', function (Container $container) {
    $router = new Dispatcher();

    $request = $container->make('http.request');

    $defaultRoutePath = __ROOT__ . 'config/route/route.php';

    $domainDeploy = config('domain-deploy');
    // 判断是否开启了子域名部署
    if ($domainDeploy) {
        $domains   = config('domain-deploy-config');
        $module    = Arr::getValue($domains, $request->host());
        $routeFile = __ROOT__ . 'config/route/' . $module . '.php';

        if (! is_file($routeFile)) {
            $routeFile = & $defaultRoutePath;
        }
    } else {
        $routeFile = & $defaultRoutePath;
    }

    $router->fill((array) include $routeFile);

    return $router;
});


