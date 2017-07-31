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
                    <span style="font-size: 1.2em"><?php echo trans_with_global('Search Items')?></span>
                    <div class="pull-right"><a data-action="toggle-search-content" class="btn btn-purple btn-trans"><?php echo trans_with_global('Hidden')?></a></div>
                </div>
                <div style="border-bottom: 1px solid #DBDDDE;margin-top: 13px;"></div>

                <div class="search-card-box-content">
                    <div style="height: 1em"></div>


                    <!-- items row -->
                    <div class="icon-list-demo row">
                        <?php echo component_view('fields/enum/align-search', [
                            'name' => 'type',
                            'options' => [1, 2]
                        ])?>
                    </div>
                    <!-- items row end -->

                    <!-- items row -->
                    <div class="icon-list-demo row">
                        <?php echo component_view('fields/enum/fliter-search', [
                            'name' => 'status',
                            'options' => [1, 2]
                        ]);?>

                
                    </div>
                    <!-- items row end -->

                    <!-- items row -->
                    <div class="icon-list-demo row">
                        <?php echo component_view('fields/varchar/search', ['name' => 'controller']);?>

                        <?php echo component_view('fields/varchar/search', ['name' => 'name']);?>

                        <?php echo component_view('fields/varchar/date-search', ['name' => 'created_at']);?>

                    </div>
                    <!-- items row end -->

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