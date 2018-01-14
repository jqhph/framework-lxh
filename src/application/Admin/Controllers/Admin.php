<?php
/**
 * User controller
 *
 * @author Jqh
 * @date   2017/6/28 21:34
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Filter;
use Lxh\Admin\Form\Field\MultipleFile;
use Lxh\Admin\Form\Field\MultipleSelect;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Modal;
use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Admin\Admin as AdminCreator;

class Admin extends \Lxh\Admin\Http\Controllers\Admin
{
}
