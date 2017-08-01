<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 23:41
 */
$value    = isset($value) ? $value : null;
$hideLabe = isset($hideLabe) ? $hideLabe : false;
$labelCol = empty($labelCol) ? 2 : $labelCol;
$formCol  = empty($formCol) ? 8 : $formCol;
?>

<div class="form-group clearfix">
    <?php if (! $hideLabe) {?>
        <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($name, 'fields'); ?></label>
    <?php }?>
    <div class="col-md-<?php echo $formCol;?>">
        <input value="<?php echo $value;?>" type="hidden"  name="<?php echo $name;?>" />
        <table class="table table-bordered m-0">

            <tr>
                <th>用户</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td><div class="checkbox pull-left">查看</div><div class="checkbox checkbox-primary pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">新增</div><div class="checkbox checkbox-success pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">编辑</div><div class="checkbox checkbox-warning pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">删除</div><div class="checkbox checkbox-danger pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
            </tr>
            <tr><td colspan="4" style="border-left: 1px solid #fff;border-right: 1px solid #fff;"></td></tr>
            <tr>
                <th>用户</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td><div class="checkbox pull-left">查看</div><div class="checkbox checkbox-primary pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">新增</div><div class="checkbox checkbox-success pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">编辑</div><div class="checkbox checkbox-warning pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
                <td><div class="checkbox pull-left">删除</div><div class="checkbox checkbox-danger pull-left" style="margin-left: 10px;">
                        <input type="checkbox" checked=""><label></label></div></td>
            </tr>


        </table>
        <a class="btn btn-danger btn-trans hidden"><?php echo trans_with_global('detail')?>&nbsp;<i class="fa fa-search-plus"></i></a>
    </div>
</div>
