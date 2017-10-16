<?php
/**
 * 权限管理
 *
 * @author Jqh
 * @date   2017/8/2 14:08
 */

namespace Lxh\Admin\Acl;

use Lxh\Admin\Model\User;

class Permit
{
    // 读取数据action方法
    const READ_ACTION = 'list';

    // 新增数据action方法
    const CREATE_ACTION = 'create';

    // 修改数据action方法
    const UPDATE_ACTION = 'detail';

    // 删除数据action方法
    const DELIETE_ACTION = 'delete';

    // 导入数据action方法
    const IMPORT_ACTION = 'import';

    // 导出数据action方法
    const EXPORT_ACTION = 'export';

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = admin();
    }

    /**
     * 判断当前角色是否是管理员
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user->isAdmin();
    }

    /**
     * 判断角色是否有访问某方法的权限
     *
     * @param  string $action
     * @param  string $controller
     * @return bool
     */
    public function access($action = __ACTION__, $controller = __CONTROLLER__)
    {
        // 管理员拥有所有权限
        if ($this->user->isAdmin()) {
            return true;
        }
        // 方法名不区分大小写
        $action = strtolower($action);

    }

    /**
     * 判断是否有读取数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessRead($controller = __CONTROLLER__)
    {
        return $this->access(static::READ_ACTION, $controller);
    }

    /**
     * 判断是否有创建数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessCreate($controller = __CONTROLLER__)
    {
        return $this->access(static::CREATE_ACTION, $controller);
    }

    /**
     * 判断是否有修改数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessUpdate($controller = __CONTROLLER__)
    {
        return $this->access(static::UPDATE_ACTION, $controller);
    }

    /**
     * 判断是否有删除数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessDelete($controller = __CONTROLLER__)
    {
        return $this->access(static::DELIETE_ACTION, $controller);
    }

    /**
     * 判断是否有导入数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessImport($controller = __CONTROLLER__)
    {
        return $this->access(static::IMPORT_ACTION, $controller);
    }

    /**
     * 判断是否有导出数据的权限
     *
     * @param string $controller
     * @return bool
     */
    public function accessExport($controller = __CONTROLLER__)
    {
        return $this->access(static::EXPORT_ACTION, $controller);
    }

}
