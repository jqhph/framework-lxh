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
                echo component_view($titles[$name]['view'], ['val' => $r[$name], 'name' => $name]);
            ?>
        </th>
    <?php }
    ?>
    
    <th><a href="<?php echo AdminUrlCreator::makeDetail($r['id']);?>">
            <i class="zmdi zmdi-edit"></i></a>&nbsp;&nbsp;
        <a data-model="<?php echo empty($model) ? __CONTROLLER__ : $model;?>" data-action="delete-row" data-id="<?php echo $r['id'];?>" href="javascript:"><i class="zmdi zmdi-delete"></i></a></th>
</tr>
