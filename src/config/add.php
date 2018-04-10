<?php
/**
 * 增加配置文件
 * 如 'mail' => 'mail',  则会用 'mail' 作为键值包含 'mail.php' 文件里面的数组
 *
 * @author Jqh
 * @date   2018/4/10 17:55
 */

return [
    // 数据库配置文件
    'db'     => __ENV__ . '/database',
    'client' => __ENV__ . '/client',
    'app'    => __ENV__ . '/app',
    'mail'   => 'mail',
    'admin'  => 'admin',
];
