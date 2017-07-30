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


<div class="row">
    <div class="col-md-12">
        <div>
            <div class="card-box">

                <h4 class="m-t-0 page-header header-title "><?php echo trans_with_global('Search Items')?></h4>
                <div style="height: 1em"></div>

                <!-- items row -->
                <div class="icon-list-demo row">
                    <div class=" col-md-12">
                        <form class="form-inline">
                            <div class="form-group ">
                                <p class="form-control-static"><strong>单选：</strong></p>
                            </div>
                            <div class="form-group fields-radio" style="width: 35%">
                                <input type="hidden" name="" value="" />
                                <a data-value="" class="btn btn-info  ">全部</a>
                                <a data-value="1" class="btn btn-success btn-trans ">选项1</a>
                                <a data-value="2" class="btn btn-purple btn-trans ">选项2</a>
                                <a data-value="3" class="btn btn-info btn-trans ">选项1</a>
                                <a data-value="4" class="btn btn-custom btn-trans ">选项1</a>
                                <a data-value="5" class="btn btn-danger btn-trans ">选项1</a>
                                <a data-value="6" class="btn btn-warning btn-trans ">选项1</a>
                                <a data-value="7" class="btn btn-pink btn-trans ">选项1</a>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- items row end -->

                <!-- items row -->
                <div class="icon-list-demo row">
                    <div class=" col-md-12">
                        <form class="form-inline">
                            <div class="form-group ">
                                <p class="form-control-static"><strong>单选：</strong></p>
                            </div>
                            <div class="form-group fields-radio" style="width: 35%">
                                <input type="hidden" name="" value="" />
                                <a data-value="" class="btn btn-info  ">全部</a>
                                <a data-value="1" class="btn btn-success btn-trans ">选项1</a>
                                <a data-value="2" class="btn btn-purple btn-trans ">选项2</a>
                                <a data-value="3" class="btn btn-info btn-trans ">选项1</a>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- items row end -->

                <!-- items row -->
                <div class="icon-list-demo row">
                    <div class=" col-md-3">
                        <form class="form-inline">
                            <div class="form-group ">
                                <p class="form-control-static"><strong>名称：</strong></p>
                            </div>
                            <div class="form-group " style="width: 70%">
                                <input class="form-control col-md-12" style="width: 100%"  type="text" value="test">
                            </div>
                        </form>
                    </div>

                    <div class=" col-md-3">
                        <form class="form-inline">
                            <div class="form-group ">
                                <p class="form-control-static"><strong>创建人：</strong></p>
                            </div>
                            <div class="form-group " style="width: 70%">
                                <input class="form-control col-md-12" style="width: 100%"  type="text" value="test">
                            </div>
                        </form>
                    </div>

                    <div class=" col-md-6">
                        <form class="form-inline">
                            <div class="form-group ">
                                <p class="form-control-static"><strong>时间：</strong></p>
                            </div>
                            <div class="form-group " style="width: 35%">
                                <input class="form-control col-md-12" style="width: 100%"  type="text" value="test">
                            </div>
                            &nbsp;-&nbsp;
                            <div class="form-group " style="width: 35%">
                                <input class="form-control col-md-12" style="width: 100%"  type="text" value="test">
                            </div>
                        </form>
                    </div>

                </div>
                <!-- items row end -->

                <div style="height: 1em"></div>
                <div class="pull-left">
                    <a data-action="page-search" class="btn btn-primary ">&nbsp;&nbsp;&nbsp;<i class="fa fa-search"></i>&nbsp;&nbsp;&nbsp;</a>&nbsp;
                    <a data-action="page-search-reset" class="btn btn-default"><?php echo trans_with_global('Reset'); ?></a>
                </div>
                <div class="clearfix"></div>

            </div>
        </div>
    </div>

</div>



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
    add_js('view/fields/enum/search-items');
    // 引入index界面公共js
    add_js('view/public-index');
//    console.log(111, ResponsiveTable)
</script>
