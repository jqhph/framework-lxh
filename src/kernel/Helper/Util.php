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

    /**
     * 把php数据转化成文本形式，并以"return"形式返回
     *
     * @param array $array
     * @param bool  $numericKey 是否输出数字键值，默认true
     * @return string
     */
    public static function arrayToReturnText(array & $array, $numericKey = true)
    {
        return "<?php \nreturn " . static::arrayToText($array, $numericKey) . ";\n";
    }

    /**
     * 把php数据转化成文本形式
     *
     * @param array $array
     * @param bool  $numericKey 是否输出数字键值，默认true
     * @param int   $level
     * @return string
     */
    public static function arrayToText(array & $array, $numericKey = true, $level = 1)
    {
        $start = '[';
        $end   = ']';

        $txt = "$start\n";

        foreach ($array as $k => & $v) {
            if (is_array($v)) {
                if ($numericKey) {
                    $pre = is_string($k) ? "'$k' => " : "$k => ";
                } else {
                    $pre = is_string($k) ? "'$k' => " : '';
                }

                $txt .= static::makeBlank($level) . $pre . static::arrayToText($v, $numericKey, $level + 1) . ",\n";

                continue;
            }
            $t = $v;

            if ($v === true) {
                $t = 'true';
            } elseif ($v === false) {
                $t = 'false';
            } elseif ($v === null) {
                $t = 'null';
            } elseif (is_string($v)) {
                $v = str_replace("'", "\\'", $v);
                $t = "'$v'";
            }

            if ($numericKey) {
                $pre = is_string($k) ? "'$k' => " : "$k => ";
            } else {
                $pre = is_string($k) ? "'$k' => " : '';
            }

            $txt .= static::makeBlank($level) . "{$pre}{$t},\n";
        }

        $txt = rtrim($txt, "\n");

        return "$txt\n" . static::makeBlank($level - 1) . "$end";
    }

    // 输出空格（4个空格缩进）
    public static function makeBlank($level)
    {
        $txt = '';
        for ($i = 0; $i < $level; $i ++) {
            $txt .= '    ';
        }
        return $txt;
    }

    /**
     * 合并新的数组到旧的数组
     *
     * @param  array $content
     * @param  array $new
     * @return array
     */
    public static function merge(array & $content, array & $new, $recurrence = false)
    {
        foreach ($new as $k => & $v) {
            if ($recurrence) {
                if (isset($content[$k]) && is_array($content[$k]) && is_array($v)) {
                    $content[$k] = static::merge($content[$k], $v, true);
                    continue;
                }
            }

            $content[$k] = $v;
        }

        return $content;
    }

    /**
     * Unset a value in array recursively
     *
     * @param  array  $haystack
     * @param  string $needle
     * @return array
     */
    public static function unsetInArrayByValue(array & $haystack, $needle)
    {
        foreach($haystack as $key => & $value) {
            if (is_array($value)) {
                $haystack[$key] = static::unsetInArrayByValue($needle, $value);
            } else if ($needle === $value) {

                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    /**
     * 'User.fields.id' 转化为
     *
     *  [
     *      'User' => [
     *          'fields' => ['id']
     *      ]
     * ]
     *
     * @param
     * @return void
     */
    public static function multilayerStringToArray($str)
    {
        $data = [];

        $tmp = explode('.', $str);

        $last = count($tmp) - 1;

        foreach ($tmp as $k => & $v) {
            if ($k == $last) {
                static::appendValueToArray($data, $v, true);
            } else {
                static::appendValueToArray($data, $v);
            }
        }

        return $data;
    }

    // 追加元素到数组最底层
    public static function appendValueToArray(array & $data, $value, $toValue = false)
    {
        if (empty($data)) {
            if ($toValue) {
                $data[] = $value;
            } else {
                $data[$value] = [];
            }
            return;
        }
        foreach ($data as $k => & $v) {
            if (empty($v)) {
                if ($toValue) {
                    $data[$k] = [$value];
                } else {
                    $data[$k][$value] = [];
                }
                break;
            }

            static::appendValueToArray($v, $value, $toValue);
        }
    }


    /**
     * Unset content items defined in the unset.json
     *
     * @param array $content
     * @param string | array $unsets in format
     *   [
     *      'key1' => ['unsetKey1', 'unsetKey2'],
     *      'key2' => ['unsetKey1', 'unsetKey2'],
     *      'key3' => ['key' => ['unsetKey3', 'unsetKey4']]
     *  ]
     *  OR
     *  ['key.unset1', 'key1.key2.unset2', .....]
     *  OR
     *  'key1.unset1'
     *
     * @return array
     */
    public static function unsetInArray(array & $content, $unsets)
    {
        if (empty($unsets)) {
            return $content;
        }

        foreach((array) $unsets as $key => & $unsetItem) {
            if (is_string($unsetItem)) {
                $unsetItem = static::multilayerStringToArray($unsetItem);
            }

            foreach ($unsetItem as $k => & $v) {
                if (is_array($v) && isset($content[$k]) && is_array($content[$k])) {
                    $content[$k] = static::unsetInArray($content[$k], $unsetItem);

                    continue;
                }
                unset($content[$v]);
            }
        }

        return $content;
    }

}
