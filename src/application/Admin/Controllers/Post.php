<?php

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Popover;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Http\Controllers\Controller;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Tab;
use Lxh\Admin\Widgets\WaterFall;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Post extends Controller
{
    public function actionList(array $params)
    {
        $content = $this->admin()->content();

        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        $content->row(function (Row $row) {
            $wf = new WaterFall();

            $row->column(12, $wf);
        });

        return $content->render();
    }
}
