<?php
/**
 * 状态码
 *
 * @author Jqh
 * @date   2017/6/16 14:23
 */

namespace Lxh;

class Status
{
    const SUCCESS = 10001;
    const FAILED  = 10002;
    // 参数错误
    const INVALID_ARGUMENTS = 10003;

    // 用户身份鉴权失败
    const NOT_AUTH = 10008;
}
