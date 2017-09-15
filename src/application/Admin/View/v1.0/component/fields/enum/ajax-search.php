<?php
/**
 * 带ajax搜索的单选搜索项
 *
 * @author Jqh
 * @date   2017/7/31 15:23
 */
$value = isset($value) ? $value : get_value($_REQUEST, $name, false);
?>
<div class=" col-md-4">
    <form class="form-inline fields-radio-search">
        <div class="form-group ">
            <p class="form-control-static"><span><?php echo trans($name, 'fields') . trans_with_global(':');?></span></p>
        </div>
        <div class="form-group " style="width: 26%">
            <input placeholder="<?php echo trans_with_global('Search')?>" class="form-control col-md-12" style="width: 100%" type="text" value="">
        </div>
        <div class="form-group input-group " style="width: 46%">
            <input value="<?php echo $value;?>" type="text" disabled name="<?php echo $name;?>" class="form-control"  style="width: 100%">
            <span data-action="reset-enum-text" class="input-group-addon btn-primary btn-trans" style="padding: 6px 5px"><i class="zmdi zmdi-refresh-alt"></i></span>
        </div>
    </form>
</div>

