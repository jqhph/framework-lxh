<?php
/**
 * 列表界面行
 *
 * @author Jqh
 * @date   2017/8/1 09:33
 */
use Lxh\Admin\Kernel\Url;
?>
<tr>
    <?php foreach ($items as $name => & $v) { ?>
    <th><?php
        if (! empty($v['view'])) {
            echo render_view('component.fields/' . $v['view'], ['val' => get_value($r, $name), 'name' => $name]);
        } else {
            echo get_value($r, $name);
        }
        ?>
    </th>
    <?php } ?>
    <th><a href="<?php echo Url::makeDetail($r['id']);?>">
            <?php echo trans('detail')?></a>&nbsp;&nbsp;
        <a style="color:#ff5b5b" data-model="<?php echo empty($model) ? __CONTROLLER__ : $model;?>" data-action="delete-row" data-id="<?php echo $r['id'];?>" href="javascript:"><i class="zmdi zmdi-close"></i></a></th>
</tr>