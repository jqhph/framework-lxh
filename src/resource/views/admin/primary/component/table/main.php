<?php use Lxh\Admin\Kernel\Url; ?>

<!--col-sm-12-->
<div class="<?php echo empty($topClass) ? '' : $topClass; ?>">
    <div class="card-box">

        <div class="table-rep-plugin">
            <div class="table-responsive" data-pattern="priority-columns">
                <table id="tech-companies-1" class="table  table-striped">
                    <thead>
                    <tr>
                        <?php
                        foreach ($titles as $k => & $v) {?>
                        <th class="<?php echo get_value($v, 'class');?>"
                            data-priority="<?php echo get_value($v, 'priority', 1);?>"><?php echo trans($k, 'fields'); ?></th>
                        <?php }

                        if (! empty($useAction)) {
                            // 是否开启动作
                        ?>
                        <th></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($list as $k => & $r) {?>
                    <tr>
                        <?php foreach ($titles as $name => & $v) { ?>
                        <th><?php
                            if (! empty($titles[$name]['view'])) {
                                echo render_view('component.' . $titles[$name]['view'], ['val' => $r[$name], 'name' => $name]);
                            } else {
                                echo $r[$name];
                            }
                            ?>
                        </th>
                        <?php }

                        if (! empty($useAction)) {
                        ?>
                        <th><a href="<?php echo Url::makeDetail($r['id'], isset($controller) ? $controller : __CONTROLLER__);?>">
                                <i class="zmdi zmdi-edit"></i></a>&nbsp;&nbsp;
                            <a href="javascript:"><i class="zmdi zmdi-delete"></i></a></th>
                        <?php } ?>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<?php
if (! empty($useRwd)) {
    echo load_css('rwd-table.min', 'lib/plugins/RWD-Table-Patterns/dist/css');
    echo load_js('rwd-table', 'plugins/RWD-Table-Patterns/dist/js');
}
?>