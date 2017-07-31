<?php
/**
 * 普通下拉选框搜索
 *
 * @author Jqh
 * @date   2017/7/31 15:36
 */

$value = isset($value) ? $value : get_value($_REQUEST, $name, false);

?>
<div class=" col-md-3">
    <form class="form-inline">
        <div class="form-group ">
            <p class="form-control-static"><strong><?php echo trans($name, 'fields') . trans_with_global(':');?></strong></p>
        </div>
        <div class="form-group " style="width: 70%">
            <select name="<?php echo $name;?>" class="form-control" style="width: 100%">
                <option><?php echo trans_with_global('All')?></option>
                <?php foreach ($options as & $o) { ?>
                    <option <?php if ($value == $o) {echo 'selected';} ?> value="<?php echo $o;?>"><?php echo trans_option($o, $name);?></option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>
