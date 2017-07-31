<?php

use Lxh\Kernel\AdminUrlCreator;
?>

<!--导航路径-->
<ol class="breadcrumb">
    <li>
        指标报表
    </li>
    <li>
        <a href="#">层级指标报表</a>
    </li>
</ol>

<?php echo component_view('search-items');?>

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
                <table id="tech-companies-1" class="table  table-striped">
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
<?php

//    load_css('rwd-table.min', 'lib/plugins/RWD-Table-Patterns/dist/css');
//    load_js('rwd-table', 'plugins/RWD-Table-Patterns/dist/js');

?>
<script>
    add_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    add_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table');
    // 引入index界面公共js
    add_js('view/public-index');
//    console.log(111, ResponsiveTable)
</script>
