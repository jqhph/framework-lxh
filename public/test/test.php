<?php
//http://www.xuebuyuan.com/1676976.html
$is_bad_request = false;
$cache = true;
$doc_root_uri = $_SERVER['DOCUMENT_ROOT'] . '/';
$cachedir = $doc_root_uri . 'public/cache';
//文件类型，j为js，c为css
$type = isset($_GET['t']) ? ($_GET['t'] == 'j' || $_GET['t'] == 'c' ? $_GET['t'] : '') : '';
//存放js和css文件的基目录, 例如:?b=public.js 代表的是/public/js文件夹，出发点是网站根目录
//基目录参数不是必须的，如果有基目录那么这个基目录就会附加在文件名之前
$base = isset($_GET['b']) ? ($doc_root_uri . str_replace('.', '/', $_GET['b'])) : $doc_root_uri;
//文件名列表，文件名不带后缀名.比如基目录是
//文件名的格式是 :基目录(如果有)+文件包名+文件名
//例如:类型是j,
//   文件名public.js.jquery
//   如果有基路径且为public，
//   那么转换后的文件名就是/public/public/js/jquery.js
//   如果没有基路径
//   那么转换后的文件名就是/public/js/jquery.js
//多个文件名之间用,分隔
$fs = isset($_GET['fs']) ? str_replace('.', '/', $_GET['fs']) : '';
$fs = str_replace(',', '.' . ($type == 'j' ? 'js,' : 'css,'), $fs);
$fs = $fs . ($type == 'j' ? '.js' : '.css');
if ($type == '' || $fs == '') {
    $is_bad_request = true;
}
//die($base);
if ($is_bad_request) {
    header("HTTP/1.0 503 Not Implemented");
}
$file_type = $type == 'j' ? 'javascript' : 'css';
$elements = explode(',', preg_replace('/([^?]*).*/', '\1', $fs));
// Determine last modification date of the files
$lastmodified = 0;
while (list(, $element) = each($elements)) {
    $path = $base . '/' . $element;
    if (($type == 'j' && substr($path, -3) != '.js') ||
        ($type == 'c' && substr($path, -4) != '.css')
    ) {
        header("HTTP/1.0 403 Forbidden");
        exit;
    }
    if (substr($path, 0, strlen($base)) != $base || !file_exists($path)) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    $lastmodified = max($lastmodified, filemtime($path));
}
// Send Etag hash
$hash = $lastmodified . '-' . md5($fs);
header("Etag: \"" . $hash . "\"");
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
    stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"'
) {
    // Return visit and no modifications, so do not send anything
    header("HTTP/1.0 304 Not Modified");
    header("Content-Type: text/" . $file_type);
    header('Content-Length: 0');
} else {
    // First time visit or files were modified
    if ($cache) {
        // Determine supported compression method
        $gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
        $deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');
        // Determine used compression method
        $encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');
        // Check for buggy versions of Internet Explorer
        if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
            preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)
        ) {
            $version = floatval($matches[1]);
            if ($version < 6)
                $encoding = 'none';
            if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
                $encoding = 'none';
        }
        // Try the cache first to see if the combined files were already generated
        $cachefile = 'cache-' . $hash . '.' . $file_type . ($encoding != 'none' ? '.' . $encoding : '');
        if (file_exists($cachedir . '/' . $cachefile)) {
            if ($fp = fopen($cachedir . '/' . $cachefile, 'rb')) {
                if ($encoding != 'none') {
                    header("Content-Encoding: " . $encoding);
                }
                header("Content-Type: text/" . $file_type);
                header("Content-Length: " . filesize($cachedir . '/' . $cachefile));
                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
    }
    // Get contents of the files
    $contents = '';
    reset($elements);
    while (list(, $element) = each($elements)) {
        $path = $base . '/' . $element;
        $contents .= "\n\n" . file_get_contents($path);
    }
    // Send Content-Type
    header("Content-Type: text/" . $file_type);
    if (isset($encoding) && $encoding != 'none') {
        // Send compressed contents
        $contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
        header("Content-Encoding: " . $encoding);
        header('Content-Length: ' . strlen($contents));
        echo $contents;
    } else {
        // Send regular contents
        header('Content-Length: ' . strlen($contents));
        echo $contents;
    }
    // Store cache
    if ($cache) {
        if ($fp = fopen($cachedir . '/' . $cachefile, 'wb')) {
            fwrite($fp, $contents);
            fclose($fp);
        }
    }
}