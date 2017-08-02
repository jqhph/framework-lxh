<?php

use Lxh\Kernel\AdminUrlCreator;
?>

<?php echo component_view('search-items', [
    'opts' => [
        [
            ['view' => 'enum/align-search', 'vars' => ['name' => 'type','options' => [1, 2]]],
        ],
        [
            ['view' => 'varchar/search', 'vars' => ['name' => 'controller']],
            ['view' => 'enum/fliter-search', 'vars' => ['name' => 'status','options' => [1, 2]]],

        ],
        [
            ['view' => 'varchar/search', 'vars' => ['name' => 'name']],
            ['view' => 'varchar/date-search', 'vars' => ['name' => 'created_at']],
        ],
    ]
]);?>

<!--col-sm-12-->
<div class="">
    <div class="card-box">
        <div class="table-rep-plugin">
            <div class="btn-toolbar" >
                <div class="btn-group dropdown-btn-group pull-right">
                    <a href="<?php echo AdminUrlCreator::makeAction('Create'); ?>" data-action="create-row" class="btn btn-success"><?php echo trans('Create Menu'); ?></a>
                </div>
            </div>

            <div class="table-responsive" data-pattern="priority-columns">
                <table id="tech-companies-1" class="table ">
                    <thead>
                    <tr>
                        <?php
                        foreach ($titles as $k => & $v) {?>
                            <th class="<?php echo get_value($v, 'class');?>"
                                data-priority="<?php echo get_value($v, 'priority', 1);?>"><?php echo trans($k, 'fields'); ?></th>
                        <?php }
                            ?>
                            <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($list as $k => & $r) {
                        echo fetch_view('row', 'Menu', ['r' => $r, 'level' => 0]);

                        // 如果有子菜单则展示， 最多展示三层
                        if (! empty($r['subs'])) {
                            $secondMenuCount = count($r['subs']) - 1;
                            foreach ($r['subs'] as $k => & $r) {
                                $end = false;
                                if ($k == $secondMenuCount) {
                                    $end = true;
                                }
                                echo fetch_view('row', 'Menu', ['r' => $r, 'level' => 1, 'end' => $end]);

                                if (! empty($r['subs'])) {
                                    $thirdMenuCount = count($r['subs']) - 1;

                                    // 如果有三级菜单，则展示
                                    foreach ($r['subs'] as $k => & $r) {
                                        $end = false;
                                        if ($k == $thirdMenuCount) {
                                            $end = true;
                                        }
                                        echo fetch_view('row', 'Menu', ['r' => $r, 'level' => 2, 'end' => $end]);
                                    }
                                }
                            }
                        }
                    } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<script>
    add_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    add_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table');
    // 引入index界面公共js
    add_js('view/public-index');
</script>
