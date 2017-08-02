<?php
/**
 * 列表界面公共模板
 *
 * @author Jqh
 * @date   2017/8/1 09:27
 */
use Lxh\Kernel\AdminUrlCreator;

$rowView = isset($rowView) ? $rowView : 'list/row';

$scope = isset($scope) ? $scope : __CONTROLLER__;

$createUrl = AdminUrlCreator::makeAction('Create');

$createBtnText = "Create $scope";

// 搜索项界面
if (! empty($searchItems))  echo component_view('search-items', $searchItems);
?>
<!--col-sm-12-->
<div class="col-md-12">
    <div class="card-box">
        <div class="table-rep-plugin">
            <div class="btn-toolbar" >
                <div class="btn-group dropdown-btn-group pull-right">
                    <a href="<?php echo $createUrl; ?>" data-action="create-row" class="btn btn-success"><?php echo trans($createBtnText); ?></a>
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
                        <?php } ?>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($list as $k => & $r) {
                        echo component_view($rowView, ['r' => $r]);
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
