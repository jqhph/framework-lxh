<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 23:41
 */
$value    = isset($value) ? $value : 1;
$hideLabe = isset($hideLabe) ? $hideLabe : false;
$labelCol = empty($labelCol) ? 2 : $labelCol;
$formCol  = empty($formCol) ? 8 : $formCol;
$columns = isset($columns) ? $columns : 4;
$labelCategory = isset($labelCategory) ? $labelCategory : 'labels';

?>
<div class="form-group clearfix">
    <?php if (! $hideLabe) {?>
        <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($name, 'fields'); ?></label>
    <?php }?>
    <div class="col-md-<?php echo $formCol;?>">
        <table class="table table-bordered m-0">
            <?php foreach ($list as & $r) {
                if (! empty($r['title'])) {
                ?>
            <tr><th colspan="<?php echo $columns?>"><?php echo trans($r['title'], $labelCategory)?></th></tr>
            <?php } $rowsEnd = count($r['rows']) - 1; foreach ($r['rows'] as $k => & $items) { ?>
            <tr>
                <?php
                // 计算colspan
                $itemsNum = count($items);
                $tdColumns = $columns - $itemsNum;
                $len = $itemsNum - 1;
                foreach ($items as $k => & $v) { ?>
                <td <?php echo $k == $len ? "colspan='$tdColumns'" : '';?>><?php if ($v) {
                        $checkboxName = "{$name}[]";
                        $value = isset($v['value']) ? $v['value'] : 1;
                        $checked = isset($v['checked']) ? $v['checked'] : false;
                        ?><div class="checkbox pull-left"><?php echo trans($v['name'], $labelCategory)?></div>
                    <div class="checkbox checkbox-<?php echo isset($v['color']) ? $v['color'] : 'primary';?> pull-left" style="margin-left: 10px;">
                        <input <?php if ($checked) echo 'checked';?> name="<?php echo $checkboxName;?>" type="checkbox" value="<?php echo $value;?>" ><label></label></div> <?php } ?></td>
                <?php } ?>
            </tr>
            <?php }  ?>
            <tr><td colspan="<?php echo $columns?>" style="border-left: 1px solid #fff;border-right: 1px solid #fff;"></td></tr>
            <?php }  ?>
        </table>
    </div>
</div>
