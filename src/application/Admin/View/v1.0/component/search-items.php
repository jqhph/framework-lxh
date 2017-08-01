<?php
/**
 * 搜索项
 *
 * @author Jqh
 * @date   2017/7/31 12:27
 */
?>

<div class="row">
    <div class="col-md-12">
        <div>
            <div class="card-box search-form">
                <div class="card-box-header">
                    <span class="card-box-title"><?php echo trans_with_global('Search Items')?></span>
                    <div class="pull-right"><a data-action="toggle-search-content" class="btn btn-purple btn-trans"><?php echo trans_with_global('Hidden')?></a></div>
                </div>
                <div class="card-box-line"></div>
                <div class="search-card-box-content">
                    <div style="height: 1em"></div>
                    <?php
                    // 循环输出搜索选项视图
                    foreach ($opts as & $row) { ?>
                    <div class="icon-list-demo row">
                        <?php foreach ($row as & $item) {
                            // 列
                            echo component_view("fields/{$item['view']}", $item['vars']);
                        } ?>
                    </div>
                    <?php } ?>
                    <div style="height: 1em"></div>
                    <div class="pull-left">
                        <a data-action="page-search" class="btn btn-primary "><?php echo trans_with_global('Search')?>&nbsp; <i class="fa fa-search"></i></a>&nbsp;
                        <a data-action="page-search-reset" class="btn btn-default"><?php echo trans_with_global('Reset'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>add_js(['view/search-items'])</script>