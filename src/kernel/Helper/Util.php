<?php
/**
 * 工具类
 *
 * User: Jqh
 * Date: 2017/7/1
 * Time: 10:47
 */

namespace Lxh\Helper;

class Util
{
    /**
     * Convert name to Camel Case format, ex. camel_case to camelCase
     *
     * @param  string  $name
     * @param  string | array  $symbol
     * @param  boolean $capitaliseFirstChar
     *
     * @return string
     */
    public static function toCamelCase($name, $capitaliseFirstChar = false)
    {
        if ($capitaliseFirstChar) {
            return ucfirst(preg_replace_callback('/_([a-z])/', 'static::toCamelCaseConversion', $name));
        }
        return preg_replace_callback('/_([a-z])/', 'static::toCamelCaseConversion', $name);
    }

    protected static function toCamelCaseConversion($matches)
    {
        return ucfirst($matches[1]);
    }

    /**
     * Convert name from Camel Case format to underscore.
     * ex. camelCase to camel_case
     *
     * @param string | array $name
     * @param string $trim
     * @return string
     */
    public static function toUnderScore($name, $trim = false)
    {
        if ($trim) {
            return ltrim(preg_replace_callback('/([A-Z])/', 'static::toUnderline', $name), '_');
        }
        return preg_replace_callback('/([A-Z])/', 'static::toUnderline', $name);
    }

    protected static function toUnderline(& $text)
    {
        return '_' . strtolower($text[1]);
    }

    /**
     * 查询手机号归属地
     *
     * @date   2017-3-6 下午3:39:14
     * @author jqh
     * @param  int $mobile
     * @return array
     */
    public static function checkMobile($mobile)
    {
        static $types = [
            '联通' => 3, '电信' => 1, '移动' => 2
        ];

        static $area = [
            '全国' => 100, '北京' => 1, '天津' => 2, '上海' => 3, '重庆' => 4, '河北' => 5, '山西' => 6, '内蒙古' => 7,
            '辽宁' => 8, '吉林' => 9, '黑龙江' => 10, '江苏' => 11, '浙江' => 12, '安徽' => 13, '福建' => 14, '江西' => 15,
            '山东' => 16, '河南' => 17, '湖北' => 18, '湖南' => 19, '广东' => 20, '广西' => 21, '海南' => 22, '四川' => 23,
            '贵州' => 24, '云南' => 25, '西藏' => 26, '陕西' => 27, '甘肃' => 28, '青海' => 29, '宁夏' => 30, '新疆' => 31,
            '台湾' => 32,
        ];

        $rule = '/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/';
        $tag  = preg_match($rule, $mobile);
        if ($tag == 0) {
            $return['status'] = false;
            $return['msg']    = "失败：手机号不符合基本规则。";
            return $return;
        }

        //  聚合数据API
        $appkey = '36a01b916dea94800fa5846fc4bae1a9'; #通过聚合申请到数据的appkey

        $url = 'http://apis.juhe.cn/mobile/get'; #请求的数据接口URL

        $params = '?key=' . $appkey . '&phone=' . $mobile;

        $content = json_decode(file_get_contents($url . $params), true);

        $return['status'] = false;
        $return['msg']    = $content['reason'];

        if ($content) {
            #错误码判断
            $error_code = $content['error_code'];
            if ($error_code == 0) {
                $return['province']    = $area[$content['result']['province']];
                $return['company']     = $types[$content['result']['company']];
                $return['province_zh'] = $content['result']['province'];
                $return['company_zh']  = $content['result']['company'];
                $return['status']      = true;
            }
        }

        return $return;
    }

    // 二维数组按某个字段值正序快速排序
    public static function quickSort(array & $sort, $k, $start, $end)
    {
        if ($start >= $end) {
            return;
        }
        $i = $start;
        $j = $end + 1;
        while (1) {
            do {
                $i++;
            } while (! ($sort[$start][$k] <= $sort[$i][$k] || $i == $end));

            do {
                $j--;
            } while (! ($sort[$j][$k] <= $sort[$start][$k] || $j == $start));


            if ($i < $j) {
                $temp 	  = $sort[$i];
                $sort[$i] = $sort[$j];
                $sort[$j] = $temp;
            } else {
                break;
            }
        }
        $temp		  = $sort[$start];
        $sort[$start] = $sort[$j];
        $sort[$j]     = $temp;

        self::quickSort($sort, $k, $start, $j - 1);
        self::quickSort($sort, $k, $j + 1, $end);
    }

}
