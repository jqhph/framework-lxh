<?php

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Auth\Database\Permission;
use Lxh\Admin\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Routing\Controller;

class PermissionController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $content = (new Admin())->content();

        $content->header(trans('admin::lang.permissions'));
        $content->description(trans('admin::lang.list'));
        $content->body($this->grid()->render());

        return $content;
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        $content = (new Admin())->content();

        $content->header(trans('admin::lang.permissions'));
        $content->description(trans('admin::lang.edit'));
        $content->body($this->form()->edit($id));

        return $content->render();
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        $content = (new Admin())->content();

        $content->header(trans('admin::lang.permissions'));
        $content->description(trans('admin::lang.create'));
        $content->body($this->form());

        return $content->render();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return (new Admin())->grid(Permission::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->slug(trans('admin::lang.slug'));
            $grid->name(trans('admin::lang.name'));

            $grid->created_at(trans('admin::lang.created_at'));
            $grid->updated_at(trans('admin::lang.updated_at'));

            $grid->tools()->batch()->disableDelete();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = (new Admin())->form(Permission::class);

        $form->display('id', 'ID');

        $form->text('slug', trans('admin::lang.slug'))->rules('required');
        $form->text('name', trans('admin::lang.name'))->rules('required');
        $form->display('created_at', trans('admin::lang.created_at'));
        $form->display('updated_at', trans('admin::lang.updated_at'));

        return $form->render();
    }
}
