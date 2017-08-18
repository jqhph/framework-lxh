<?php use Lxh\Admin\Kernel\Url; ?>
<tr>
    <?php foreach ($titles as $name => & $v) { ?>
        <th><?php
            if (! empty($titles[$name]['view'])) {
                echo component_view($titles[$name]['view'], ['val' => $r[$name], 'name' => $name]);
            } else {
                if ($name == 'name' && $level != 0) {
//                    for ($i = 0; $i < $level*4; $i ++) {
//                        echo "&nbsp;";
//                    }
                    if ($level == 1 && empty($end)) {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;├─ {$r[$name]}";
                    } elseif ($level == 1 && $end) {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;└─ {$r[$name]}";

                    } elseif ($level == 2 && empty($end)) {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ {$r[$name]}";
                    } elseif ($level == 2 && $end) {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─ {$r[$name]}";
                    }
                } else {
                    echo $r[$name];
                }
            }
            ?>
        </th>
    <?php }
    ?>
    <th><a class="" href="<?php echo Url::makeDetail($r['id']);?>">
            <i class="fa fa-search-plus"></i></a>&nbsp;&nbsp;
        <a style="color:#ff5b5b" data-model="<?php echo empty($model) ? __CONTROLLER__ : $model;?>" data-action="delete-row" data-id="<?php echo $r['id'];?>" href="javascript:"><i class="zmdi zmdi-delete"></i></a></th>
</tr>