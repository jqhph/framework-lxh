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
                <td>查看</td>
                <td>新增</td>
                <td>编辑</td>
                <td>删除</td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <th>用户</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>查看</td>
                <td>新增</td>
                <td>编辑</td>
                <td>删除</td>
            </tr>


        </table>
        <a class="btn btn-danger btn-trans"><?php echo trans_with_global('detail')?>&nbsp;<i class="fa fa-search-plus"></i></a>
    </div>
</div>
