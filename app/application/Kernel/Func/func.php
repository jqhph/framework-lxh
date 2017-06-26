<?php
/**
 * 公共业务函数
 *
 * @author Jqh
 * @date   2017/6/15 15:17
 */

function rtx_send($msg, $rtxfile = "", $receivers = '')
{
    $receivers = $receivers ?: config('rtx-receivers');
    // 异步发送邮件:开始
    $post_arr = array(
        'sender' => '系统机器人',
        'receivers' => $receivers,
        'msg' => $msg,
        'url' => $rtxfile,
    );

    container('http.client')->post('http://rtx.fbeads.cn:8012/sendInfo.php', $post_arr);
}
