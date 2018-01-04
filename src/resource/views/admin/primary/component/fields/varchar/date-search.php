<?php
/**
 * 时间日期文本搜索框
 *
 * @author Jqh
 * @date   2017/7/31 15:58
 */
$startName = "{$name}[]";
$endName = "{$name}[]";

$value = get_value($_REQUEST, $name);

// 默认值
$start = isset($start) ? $start : '';
$end   = isset($end)   ? $end   : '';

if (is_array($value)) {
    $start = $value[0];
    $end   = $value[1];
}

?>
<div class=" col-md-6 " >
    <form class="form-inline ">
        <div class="form-group ">
            <p class="form-control-static"><span><?php echo trans($name, 'fields') . trans_with_global(':');?></span></p>
        </div>
        <div class="form-group">
            <div class="input-daterange input-group date-search-box" >
                <input value="<?php echo $start;?>" type="text" class="form-control" name="<?php echo $startName;?>"  placeholder="<?php echo trans_with_global('start')?>">
                <span class="input-group-addon btn-custom btn-trans b-0 text-white">to</span>
                <input value="<?php echo $end;?>" type="text" class="form-control" name="<?php echo $endName;?>"  placeholder="<?php echo trans_with_global('end')?>">
            </div>
        </div>
    </form>
</div>
<script>require_css(['css/bootstrap-datetimepicker.min.css']), require_js('lib/js/bootstrap-datetimepicker.min')</script>


