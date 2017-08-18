<?php
/**
 * 公共业务函数
 *
 * @author Jqh
 * @date   2017/6/15 15:17
 */

use Lxh\Admin\Acl\Permit;
use Lxh\Kernel\Support\Page;
use Lxh\Kernel\Cache\Cache;
use Lxh\Home\Url as HomeUrl;
use Lxh\Ucenter\Url as UcenterUrl;
use Lxh\Home\Kernel\Currency;

/**
 * @return Page
 */
function pages()
{
    return make('page');
}



/**
 * 权限管理
 *
 * @return Permit
 */
function acl()
{
    static $instance = null;

    return $instance ?: ($instance = new Permit());
}

/**
 * 缓存
 *
 * @param string $name 名称。当缓存类型为文件时，此参数表示缓存目录
 * @param string $driver 缓存类型，File
 * @return Cache
 */
function cache($name = '', $driver = null)
{
    static $instances = [];

    $driver = $driver ?: config('cache-driver', 'File');

    $key = $name . $driver;

    if (isset($instances[$key])) return $instances[$key];

    $class = "\\Lxh\\Kernel\\Cache\\{$driver}";

    return $instances[$key] = new $class($name);
}

/**
 * url生成器
 *
 * @return HomeUrl
 */
function home_url()
{
    static $instance = null;

    return $instance ?: ($instance = new HomeUrl());
}

/**
 * url生成器
 *
 * @return UcenterUrl
 */
function ucenter_url()
{
    static $instance = null;

    return $instance ?: ($instance = new UcenterUrl());
}

/**
 * 币别管理对象
 *
 * @return Currency
 */
function currency()
{
    static $instance = null;

    return $instance ?: ($instance = new Currency());
}

// 处理价格
function normalize_price($price, $discount = 0)
{
    $rate = currency()->rate();         //美元兑其他币种汇率

    // 人民币转化为美元
    $usdPrice = return_usd_price($price);

    /**
     * 1.价格保留2位小数
     * 2.将数据库里面的人民币跟我我们内部规定的汇率转换成美元
     */
    if ($discount > 0) {
        return ceil(($usdPrice * ((100 - $discount) / 100) * $rate) * 100) / 100;
    }

    return ceil(($usdPrice * $rate) * 100) / 100;
}

function return_usd_price($price)
{
    $defaultExchangeRate = config('default-exchange-rate', 8.2);//美元对人民币的转换率 一般为 8.2
    $priceControl = config('price_control', 1);
    /**
     * 1.价格保留3位小数
     * 2.perlin 的价格为 beads 基准价格的1.5倍
     */
    return ceil(($price * $priceControl / $defaultExchangeRate) * 100) / 100;
}

// 获取国家配置数组
function get_countries_data()
{
    static $data = null;

    if ($data !== null) return $data;

    $path = __DATA_ROOT__ . 'cache/global/allCountries';

    if (is_file($path)) {
        return $data = include $path;
    }
    return $data;
}
