<!-- Top Bar Start -->
<?php

use Lxh\Kernel\AdminUrlCreator;

echo fetch_view('top-bar', 'Public', ['nav' => trans('Making modules')]); ?>

<div class="">
    <div class="card-box p-b-0">
        <form class="System-form" onsubmit="return false">
            <div class="dropdown pull-right">

                <div class="btn-group dropdown">
                    <button type="button" class="btn btn-custom  dropdown-toggle waves-effect waves-light"  data-toggle="dropdown" aria-expanded="false"><?php echo trans('Preview code'); ?></button>
                    <button type="button" class="btn btn-custom dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </div>
            </div>

            <h4 class="header-title m-t-0 m-b-30"><?php echo trans_with_global('Primary');?></h4>

            <div id="progressbarwizard" class="pull-in">
                <ul class="nav nav-tabs navtab-wizard nav-justified bg-muted">
                    <li class="active"><a data-action="tab" href="#module-2" data-toggle="" aria-expanded="true">
                            <?php echo trans('Module information');?></a></li>
                    <li class=""><a data-action="tab"  href="#field-2" data-toggle="" aria-expanded="false">
                            <?php echo trans('Database field information');?></a></li>
                    <li class=""><a data-action="tab" href="#finish-2" data-toggle="" aria-expanded="false">
                            <?php echo trans('Extra'); ?>
                        </a></li>
                </ul>

                <div class="tab-content b-0 m-b-0">

                    <div id="bar" class="progress progress-striped progress-bar-primary-alt">
                        <div class="bar progress-bar progress-bar-primary" style="width: 33.3333%;"></div>
                    </div>

                    <div class="tab-pane p-t-10 fade active in" id="module-2">
                        <div class="row">
                            <?php echo fetch_view('modules-info');?>
                        </div>
                    </div>

                    <div class="tab-pane p-t-10 fade" id="field-2">
                        <div class="row">
                            <?php echo fetch_view('modules-add-fields');?>
                        </div>
                    </div>

                    <div class="tab-pane p-t-10 fade" id="finish-2">
                        <div class="row">
                            <?php echo fetch_view('modules-extra');?>
                        </div>
                    </div>
                    <ul class="pager m-b-0 wizard">
                        <li class="previous "><a  data-action="prev-tab"  href="#" class="btn btn-primary waves-effect waves-light">
                                <?php echo trans_with_global('Previous');?></a></li>
                        <li class="next "><a type="submit" data-action="next-tab" href="#" class="btn btn-primary waves-effect waves-light">
                                <?php echo trans_with_global('Next');?></a></li></a></li>
                    </ul>


                </div>
            </div>

        </form>
    </div>
</div>
<script>
    // , 'lib/js/wizard'
    add_js([
        parse_view_name('System', 'Modules'),
    ]);
</script>