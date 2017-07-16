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
    <th><a href="lxhadmin/<?php echo __CONTROLLER__; ?>/view/<?php echo $r['id'];?>">
            <i class="zmdi zmdi-edit"></i></a>&nbsp;&nbsp;
        <a href="javascript:"><i class="zmdi zmdi-delete"></i></a></th>
</tr>