<?php
/**
 * 带过滤搜索的下拉选框
 *
 * @author Jqh
 * @date   2017/7/31 15:18
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
        <div class="form-group " style="width: 46%;padding-bottom: 3px;">
            <select name="<?php echo $name;?>" class="form-control"  style="width: 100%">
                <option><?php echo trans_with_global('All')?></option>
                <?php foreach ($options as & $o) { ?>
                <option <?php if ($value == $o) {echo 'selected';} ?> value="<?php echo $o;?>"><?php echo trans_option($o, $name);?></option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>
<script>require_js('view/fields/enum/search-fliter')</script>
