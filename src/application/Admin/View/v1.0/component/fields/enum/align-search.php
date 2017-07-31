<?php
/**
 * 列表排列单选搜索项
 *
 * @author Jqh
 * @date   2017/7/31 14:43
 */
$btnColors = ['success ', 'custom ', 'purple ', 'danger ', 'primary ', 'warning ', 'info ', 'pink '];
$i = 0;

$value = isset($value) ? $value : get_value($_REQUEST, $name, false);

?>

<div class=" col-md-12">
    <form class="form-inline">
        <div class="form-group " >
            <p class="form-control-static"><strong><?php echo trans($name, 'fields') . trans_with_global(':');?> </strong></p>
        </div>
        <div class="form-group fields-radio" style="width: 85%">
            <input type="hidden" name="<?php echo $name;?>" value="" />
            <a data-value="" class="btn btn-<?php echo $btnColors[$i];  if ($value !== false) echo ' btn-trans ';?>"><?php echo trans_with_global('All')?></a>
            <?php $i++; foreach ($options as & $o) { ?>
                <a data-value="<?php echo $o;?>" class="btn btn-<?php echo $btnColors[$i]; if ($value != $o) echo ' btn-trans';?> "><?php echo trans_option($o, $name);?></a>
            <?php $i++; if ($i > 8) $i = 0; } ?>
        </div>

    </form>
</div>
<script>add_js('view/fields/enum/search-items')</script>
