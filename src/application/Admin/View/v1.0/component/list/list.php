<?php
/**
 * 列表界面公共模板
 *
 * @author Jqh
 * @date   2017/8/1 09:27
 */
use Lxh\Admin\Kernel\Url;

// 行公共模板
$rowView = isset($rowView) ? $rowView : 'list/row';

// 是否引入公共js
$loadPublicJs = isset($loadPublicJs) ? $loadPublicJs : true;

// 是否使用RWD-Table
$useRWD = isset($useRWD) ? $useRWD : true;

// 模块名
$scope = isset($scope) ? $scope : __CONTROLLER__;

$createUrl = Url::makeAction('Create');

$createBtnText = "Create $scope";



// 搜索项界面
if (! empty($searchItems))  echo component_view('search-items', $searchItems);
?>
<!--col-sm-12-->
<div class="">
    <div class="card-box">
        <div class="table-rep-plugin">
            <div class="btn-toolbar" >
                <div class="btn-group dropdown-btn-group pull-right">
                    <a href="<?php echo $createUrl; ?>" data-action="create-row" class="btn btn-success"><?php echo trans($createBtnText); ?></a>
                </div>
            </div>
            <div class="dt-buttons btn-group"></div>

            <div class="table-responsive" data-pattern="priority-columns" style="margin-bottom:0px;">
                <table  class="table">
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
                        echo component_view($rowView, ['r' => $r, 'titles' => & $titles]);
                    } ?>
                    </tbody>
                </table>
                <?php if ($pages) {?>
                <div class="dataTables_paginate paging_simple_numbers pull-center"  >
                    <ul class="pagination"><?php echo $pages;?></ul>
                </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
<script>
    <?php if ($useRWD) {
    // 控制字段显示隐藏
    ?>
    add_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    add_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table');
    <?php }
        if ($loadPublicJs) {
    // 引入index界面公共js
    ?>
    add_js('view/public-index');
    <?php } ?>
</script>
