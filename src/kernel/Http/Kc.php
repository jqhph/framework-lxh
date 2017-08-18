<?php

namespace Lxh\Http;

/**
 * @file    request
 * @author  jesse <ji@kindcent.com>
 * @created Apr 13, 2012 3:59:18 PM
 */

/**
 * php实现HTTP请求封装类，遵循一次只做好一件事的Unix/Linux哲学，没有解析任何请求结果，
 * 不能执行重定向指令，如果有需求，应在调用代码里实现，返回结果放入 $this->result，头部在$this->result_head里，
 * 出错时，$this->error含有错误信息
 * KC_Request Class to request data !
 * 设置rawheaders类型, User-Agent, Accept, Referer 等
 */
class Kc
{
    private $curl_path = '/usr/bin/curl'; // freebsd curl 路径 '/usr/local/bin/curl'
    private $post_with = 'socket';        // 请求的方式：socket|curl|php_curl
    private $tmp_dir = '/tmp';           // tmp 目录
    private $cookies = array();         // 允许赋值！
    private $rawheaders = array();         // 未编码的头部
    private $timeout = 30;               // 设置超时 timeout !

    private $error = '';               // 错误消息
    private $result = '';               // 结果
    private $result_head = '';               // 返回头部
    private $async = true;                        // 是否异步(默认异步，不关心返回结果)

    private $_url = '';          // 当前需要请求的 url
    private $_parsed = '';          // url分析后返回的参数数组
    private $_cookies = '';           // 经过编码后的cookie字符串，由函数生成，不要赋值！
    private $_rawheaders = '';           // 经过整理后的rawheaders字符串，不要赋值！
    private $_postcontent = '';           // 发送的内容，由函数生成，不要赋值！
    private $_httpmethod = "POST";       // POST 或者 GET

    public function __construct()
    {
        //if(!function_exists('fsockopen')){ 
        //    exit('fsockopen function is not found!'); 
        //} 
    }

    public function cookie(array $cookies)
    {
        return $this->cookies = & $cookies;
    }

    public function get($url, $data = array())
    {
        $query = !empty($data) ? http_build_query($data) : '';
        if ($query) {
            $q = false !== strpos($url, '?') ? true : false;
            $url .= ($q ? '&' : '?') . $query;
        }
        $this->_postcontent = ''; // 清空postcontent，GET时候不需要post内容。 
        return $this->_request($url, 'GET');
    }

    public function post($url, $data = array())
    {
        $this->_postcontent = !empty($data) ? http_build_query($data) : '';
        return $this->_request($url, 'POST');
    }

    public function _request($url, $method)
    {
        if (!in_array($this->post_with, array('php_curl', 'curl', 'socket'))) {
            $this->post_with = 'socket';
        }

        $this->error = '';
        $this->result = ''; // 先清空返回值，防止上次数据残留
        $this->result_head = '';

        $this->_httpmethod = $method;
        $this->_parse_url($url);
        $this->_build_cookie();
        $this->_build_rawhead();

        if ('php_curl' == $this->post_with) {
            return $this->_request_by_php_curl($url);
        } elseif ('curl' == $this->post_with) {
            return $this->_request_by_curl($url);
        } else {
            return $this->_request_by_socket();
        }
    }

    public function _parse_url($url)
    {
        $this->_url = $url;
        $p = array_merge(array('scheme' => '', 'host' => '', 'port' => 80, 'user' => '', 'pass' => '', 'path' => '', 'query' => '', 'fragment' => ''), parse_url($url));
        $p['port'] = (int)$p['port'];
        $this->_parsed = $p;
        $this->_parsed['url'] = ($p['scheme'] ? $p['scheme'] . '://' : '') . $p['user'] . ($p['pass'] ? ':' . $p['pass'] : '') . ($p['user'] || $p['pass'] ? '@' : '') .
            $p['host'] . $p['path'] . ($p['query'] ? '?' . $p['query'] : '') . ($p['fragment'] ? '#' . $p['fragment'] : '');
        $this->_parsed['uri'] = ($p['path'] ? $p['path'] : '/') . ($p['query'] ? '?' . $p['query'] : '');
    }

    public function _build_cookie()
    {
        $cookies = '';
        if (!empty($this->cookies) && is_array($this->cookies)) {
            /**
             *  // 如果安装了pecl_http扩展，可以直接调用 http_build_cookie，依赖pecl_http模块，启用：extension=http.so
             *  if( function_exists('http_build_cookie') )
             *      $cookies = http_build_cookie(array('cookies'=>$this->cookies));
             */
            foreach ($this->cookies as $k => $v) {
                $cookies .= urlencode($k) . "=" . urlencode($v) . "; ";
            }
            $cookies = substr($cookies, 0, -2);
        }
        $this->_cookies = $cookies;
    }

    public function _build_rawhead()
    {
        $h = '';
        $ha = array();
        if (!empty($this->rawheaders) && is_array($this->rawheaders)) {
            foreach ($this->rawheaders as $k => $v) {
                $h .= "$k: $v\r\n";
                $ha[] = "$k: $v";
            }
        }
        $this->_rawheaders = 'socket' === $this->post_with ? $h : $ha;
    }

    public function _request_by_curl($url)
    {
        if (!$this->curl_path || !is_executable($this->curl_path)) {
            $this->error = 'There is not executable curl file!';
            return false;
        }
        $head = array_merge(array("HOST: {$this->_parsed['host']}"), $this->_rawheaders, array("Cookie: $this->_cookies"));
        $param = '';
        foreach ($head as $v) {
            $param .= ' -H "' . strtr($v, '"', ' ') . '"';
        }
        $param .= " -m " . $this->timeout;
        if ($this->_postcontent) {
            if (strlen($this->_postcontent) > 256) { // 超过 256个字节的post内容时，采用文件post，防止命令行字符串过长导致执行失败
                $body_file = tempnam($this->tmp_dir, 'kc_post_body');
                file_put_contents($body_file, $this->_postcontent);
                $param .= ' -d "@' . $body_file . '"';
            } else {
                $param .= ' -d "' . $this->_postcontent . '"';
            }
        }
        $header_file = tempnam($this->tmp_dir, 'kc_post_head');
        $safe_url = str_replace(array(' ', '"'), array('%20', '%22'), $url);
        $cmd = $this->curl_path . ('https' === $this->_parsed['scheme'] ? ' -k' : '') . ' -D "' . $header_file . '"' . $param . ' "' . $safe_url . '"';
        exec($cmd, $result, $return);
        if ($return) {
            $this->error = "Error: cURL could not retrieve the document, error $return.";
            return false;
        }
        $this->result = implode("\r\n", $result);
        $this->result_head = file_get_contents("$header_file");
        unlink($header_file);
        if (!empty($body_file)) unlink($body_file);
        return true;
    }

    public function _request_by_php_curl($url)
    {
        if (!function_exists('curl_init')) {
            $this->error = 'php_curl compenent is not enabled!';
            return false;
        }
        $ch = curl_init($url);
        if (!empty($this->_rawheaders))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_rawheaders);
        if (!empty($this->_cookies))
            curl_setopt($ch, CURLOPT_COOKIE, $this->_cookies);
        if ('POST' === $this->_httpmethod) { // 是否为 POST请求
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postcontent);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL安全链接不执行检查 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); // 最大执行时间
        curl_setopt($ch, CURLOPT_HEADER, true);      // 返回头部信息 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回请求后的结果字符串 
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->error = "php_curl request error: " . curl_error($ch);
            return false;
        }
        curl_close($ch); // 关闭释放资源 
        $pt = strpos($result, "\r\n\r\n");
        $this->result = substr($result, $pt + 4);
        $this->result_head = substr($result, 0, $pt + 4);
        return true;
    }

    public function _request_by_socket()
    {
        if (!function_exists('fsockopen')) {
            $this->error = 'fsockopen function could not been found!';
            return false;
        }
        $auth = !empty($this->_parsed['user']) ? base64_encode($this->_parsed['user'] . ':' . $this->_parsed['pass']) : '';
        $req = '';
        $req .= "$this->_httpmethod {$this->_parsed['uri']} HTTP/1.1\r\n";
        $req .= "Host: {$this->_parsed['host']}\r\n";
        $req .= $auth ? "Authorization: Basic " . ($auth) . "\r\n" : '';
        $req .= $this->_rawheaders ? $this->_rawheaders : '';
        $req .= $this->_cookies ? "Cookie: $this->_cookies\r\n" : '';


        if ($this->_postcontent) {
            $req .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $req .= "Content-Length: " . strlen($this->_postcontent) . "\r\n";
            $req .= "Connection: close\r\n\r\n";
            $req .= $this->_postcontent;
        } else {
            $req .= "Connection: close\r\n\r\n";
        }
        $host = $this->_parsed['host'];
        $port = $this->_parsed['port'];
        if ('https' === $this->_parsed['scheme']) {
            $host = 'ssl://' . $host;
            $port = 443;
        }

        $fp = fsockopen($host, $port, $errno, $errstr, $this->timeout);
        if (!$fp) {
            $this->error = "fsockopen open failed: $errstr!";
            return false;
        }
        fwrite($fp, $req, strlen($req));

        $this->result = '';
        $this->result_head = '';
        if (!$this->async) {
            while (!feof($fp)) {
                $line = fgets($fp);
                $this->result_head .= $line;
                if ("\r\n" === $line) break;
            }
            while (!feof($fp)) {
                $this->result .= fgets($fp);
            }
        }
        fclose($fp);
        return true;
    }

    public function __destruct()
    {
        $this->error = '';// 清空
        $this->result = '';
        $this->result_head = '';
    }
}
