<?php
/**
 * 普通vachar搜索
 *
 * @author Jqh
 * @date   2017/7/31 15:52
 */
$value = isset($value) ? $value : get_value($_REQUEST, $name, false);
?>
<div class=" col-md-3">
    <form class="form-inline">
        <div class="form-group ">
            <p class="form-control-static"><span><?php echo trans($name, 'fields') . trans_with_global(':');?></span></p>
        </div>
        <div class="form-group " style="width: 70%">
            <input value="<?php echo $value;?>" name="<?php echo $name;?>" placeholder="<?php echo empty($placeholder) ? '' : $placeholder;?>" class="form-control col-md-12" style="width: 100%"  type="text">
        </div>
    </form>
</div>
