<?php
/**
 * 列表排列单选搜索项
 *
 * @author Jqh
 * @date   2017/7/31 14:43
 */
$btnColors = ['success ', 'danger', 'custom ', 'purple', 'primary ', 'info ', ]; // 'pink '
//$btnColors = ['success', 'custom','success','danger','success','purple','success','success'];
//$btnColors = ['info', 'info','info','info','info','info','info','info'];
if (empty($GLOBALS['__i__'])) $GLOBALS['__i__'] = 0;

$GLOBALS['__i__'] ++;

$i = $GLOBALS['__i__'] - 1;

$value = isset($value) ? $value : get_value($_REQUEST, $name, false);

?>

<div class=" col-md-12">
    <form class="form-inline">
        <div class="form-group " >
            <p class="form-control-static"><span><?php echo trans($name, 'fields') . trans_with_global(':');?> </span></p>
        </div>
        <div class="form-group fields-radio" style="width: 85%">
            <input type="hidden" name="<?php echo $name;?>" value="<?php echo $value?>" id="align-search"/>
            <?php foreach ($options as & $o) { ?>
                <?php if (is_array($o)) {?>
                    <a data-value="<?php echo $o['value'];?>" class=" waves-effect waves-float btn btn-<?php echo $btnColors[$i]; if ($value != $o) echo ' btn-trans';?> ">
                        <?php echo $o['name'];?>
                    </a>
                <?php } else { ?>
                <a data-value="<?php echo $o;?>" class=" waves-effect waves-float btn btn-<?php echo $btnColors[$i]; if ($value != $o) echo ' btn-trans';?> ">
                    <?php echo trans_option($o, $name);?>
                </a>
                <?php } ?>
                    <?php  } ?>
        </div>

    </form>
</div>
<script>add_js('view/fields/enum/search-items')</script>

