<?php
/**
 * 列表界面行
 *
 * @author Jqh
 * @date   2017/8/1 09:33
 */
use Lxh\Kernel\AdminUrlCreator;
?>
<tr>
    <?php foreach ($titles as $name => & $v) { ?>
    <th><?php
        if (! empty($titles[$name]['view'])) {
            echo component_view('fields/' . $titles[$name]['view'], ['val' => $r[$name], 'name' => $name]);
        } else {
            echo $r[$name];
        }
        ?>
    </th>
    <?php } ?>
    <th><a href="<?php echo AdminUrlCreator::makeDetail($r['id']);?>">
            <i class="fa fa-search-plus"></i></a>&nbsp;&nbsp;
        <a style="color:#ff5b5b" data-model="<?php echo empty($model) ? __CONTROLLER__ : $model;?>" data-action="delete-row" data-id="<?php echo $r['id'];?>" href="javascript:"><i class="zmdi zmdi-delete"></i></a></th>
</tr>
