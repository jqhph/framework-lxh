<?php
/**
 * 公共业务函数
 *
 * @author Jqh
 * @date   2017/6/15 15:17
 */

/**
 * 分页函数
 *
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $perpage 每页显示数
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 */
function pages($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(), $setpages = 10)
{
    $setpages = 10;
    $multipage = '<nav><ul class="pagination pagination-sm">';
    if ($num > $perpage) {
        $page = $setpages + 1;
        $offset = ceil($setpages / 2 - 1);
        $pages = ceil($num / $perpage);

        $from = $curr_page - $offset;
        $to = $curr_page + $offset;
        $more = 0;
        if ($page >= $pages) {
            $from = 1;
            $to = $pages - 1;
        } else {
            if ($from <= 1) {
                $to = $page - 1;
                $from = 1;
            } elseif ($to >= $pages) {
                $from = $pages - ($page - 2);
                $to = $pages - 1;
            }
            $more = 1;
        }
        $multipage .= '<li class="disabled"><span> ' . $num . ' </span></li>';
        if ($curr_page > 0) {
            //$multipage .= ' <a href="'.pageurl($urlrule, $curr_page-1, $array).'" class="previouspage"> &lt;&lt;</a>';
            if ($curr_page == 1) {
                //$multipage .= ' <li class="active"><span>1</span></li>';
            } elseif ($curr_page > 11 && $more) {
                $multipage .= ' <li><a href="' . pageurl($urlrule, 1, $array) . '" class="firstpage"> << </a> <a href="' . pageurl($urlrule, $curr_page - 1, $array) . '" class="previouspage"> &lt;&lt;</a></li>';
            } else {
                $multipage .= ' <li><a href="' . pageurl($urlrule, 1, $array) . '" class="firstpage"> << </a>  <a href="' . pageurl($urlrule, $curr_page - 1, $array) . '" class="previouspage"> &lt;&lt;</a></li>';
            }
        }
        for ($i = $from; $i <= $to; $i++) {
            if ($i != $curr_page) {
                $multipage .= ' <li><a href="' . pageurl($urlrule, $i, $array) . '">' . $i . '</a></li>';
            } else {
                $multipage .= ' <li class="active"><span>' . $i . '</span></li>';
            }
        }
        if ($curr_page < $pages) {
            if ($curr_page < $pages - 10 && $more) {
                $multipage .= ' <li><a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="nextpage">&gt;&gt;</a> <a href="' . pageurl($urlrule, $pages, $array) . '"> >> </a></li> ';
            } else {
                $multipage .= ' <li><a href="' . pageurl($urlrule, $pages, $array) . '">' . $pages . '</a> <a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="nextpage">&gt;&gt;</a></li>';
            }
        } elseif ($curr_page == $pages) {
            $multipage .= ' <li class="active"><a >' . $pages . '</a></li> ';
        } else {
            $multipage .= ' <li><a href="' . pageurl($urlrule, $pages, $array) . '">' . $pages . '</a> <a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="nextpage"">&gt;&gt;</a></li>';
        }
    }
    return $multipage . "</ul></nav>";
}

/**
 * 返回分页路径
 *
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array())
{

    if (strpos($urlrule, '~')) {
        $urlrules = explode('~', $urlrule);
        $urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
    }
    $findme = array('{$page}', '[page]');
    $replaceme = array($page, $page);
    if (is_array($array)) foreach ($array as $k => $v) {
        $findme[] = '{$' . $k . '}';
        $replaceme[] = $v;
    }

    $url = str_replace($findme, $replaceme, $urlrule);
    $url = str_replace(array('http://', '//', '~', ' '), array('~', '/', 'http://', '+'), $url);

    if (str_exists($url, '?')) {
    } else
        if (!empty($_SERVER["QUERY_STRING"])) $url .= '?' . $_SERVER["QUERY_STRING"];

    return $url;
}
