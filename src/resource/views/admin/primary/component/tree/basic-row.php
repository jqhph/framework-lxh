<ul>
    <?php
    $parentsStr = empty($parentsStr) ? '' : $parentsStr;

    foreach ((array) $list as $dir => & $v) {
        if (is_int($dir)) {
            ?>
            <li data-jstree='{"type":"file"}'><span class="sub" data-parent="<?php echo $parentsStr;?>"><?php echo $v;?></span></li>
        <?php } elseif (is_string($dir)) { ?>
            <li data-jstree='{"opened":true}'><?php echo "<span class=\"parent\"  data-parent=\"$parentsStr\">$dir</span>";
                foreach ((array) $v as $d => & $r) {
                    if (is_string($d)) { ?>
                <ul>
                    <li data-jstree='{"opened":true}'>
                        <?php echo "<span class=\"parent\" data-parent=\"$parentsStr/$dir\">$d</span>";
                        echo view('component.tree.basic-row', ['list' => $r, 'parentsStr' => "$parentsStr/$dir/$d"])->render(); ?>
                    </li>
                </ul>
                <?php   } else {
                        echo view('component.tree.basic-row', ['list' => $r, 'parentsStr' => "$parentsStr/$dir"])->render();
                    }
                } ?>
            </li>
        <?php } ?>

    <?php } ?>
</ul>