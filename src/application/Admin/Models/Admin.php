<?php

/**
 * 用户模型
 *
 * @author Jqh
 * @date   2017/6/27 16:37
 */
namespace Lxh\Admin\Models;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Entity;
use Lxh\Mvc\Model;
use Lxh\Mvc\Session;
use Lxh\Support\Password;

class Admin extends \Lxh\Auth\Database\Admin
{
}
