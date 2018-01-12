<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Auth\Ability;
use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;
use Lxh\Admin\Layout\Row;

class Menu extends \Lxh\Admin\Http\Controllers\Menu
{

}
