<!-- Top Bar Start -->
<?php

use Lxh\Kernel\AdminUrlCreator;

echo fetch_view('top-bar', 'Public', ['nav' => trans('Making modules')]); ?>


<div class="">
    <div class="card-box p-b-0">
        <form class="System-form">


            <h4 class="header-title m-t-0 m-b-30"><?php echo trans_with_global('Primary');?></h4>

            <div id="progressbarwizard" class="pull-in">
                <ul class="nav nav-tabs navtab-wizard nav-justified bg-muted">
                    <li class="active"><a data-action="tab" href="#module-2" data-toggle="" aria-expanded="true">
                            <?php echo trans('Module information');?></a></li>
                    <li class=""><a data-action="tab"  href="#field-2" data-toggle="" aria-expanded="false">
                            <?php echo trans('Database field information');?></a></li>
                    <li class=""><a data-action="tab" href="#finish-2" data-toggle="" aria-expanded="false">Finish</a></li>
                </ul>

                <div class="tab-content b-0 m-b-0">

                    <div id="bar" class="progress progress-striped progress-bar-primary-alt">
                        <div class="bar progress-bar progress-bar-primary" style="width: 33.3333%;"></div>
                    </div>

                    <div class="tab-pane p-t-10 fade active in" id="module-2">
                        <div class="row">
                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'en_name', 'label' => 'english name', 'value' => '']); ?>

                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'zh_name', 'label' => 'chinese name', 'value' => '']); ?>

                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'author', 'label' => 'author', 'value' => '']); ?>

                            <?php echo component_view('fields/enum/edit',
                                ['name' => 'module', 'label' => 'module', 'options' => & $moduleOptions]); ?>

                            <?php echo component_view('fields/enum/edit',
                                ['name' => 'inheritance', 'label' => 'inheritance of controller', 'options' => & $controllerOptions]); ?>

                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'controller', 'label' => 'controller', 'value' => '']); ?>

                            <?php echo component_view('fields/checkbox/edit',
                                ['name' => 'actions', 'label' => 'actions', 'rows' => [
                                    ['label' => 'add', 'value' => 'add', 'checked' => 1],
                                    ['label' => 'update', 'value' => 'update', 'checked' => 1],
                                    ['label' => 'delete', 'value' => 'delete', 'checked' => 1],
                                    ['label' => 'list', 'value' => 'list', 'checked' => 1],
                                    ['label' => 'search', 'value' => 'search', 'checked' => 1],
                                    ['label' => 'order by', 'value' => 'order', 'checked' => 1],
                                    ['label' => 'display menu ', 'value' => 'displayMenu', 'checked' => 1],
                                ]]); ?>

                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'icon', 'label' => 'icon', 'value' => '']); ?>

                            <?php echo component_view('fields/varchar/edit',
                                ['name' => 'limit', 'label' => 'page limit', 'value' => 20]); ?>


                        </div>
                    </div>

                    <div class="tab-pane p-t-10 fade" id="field-2">
                        <div class="row">



                            <div class="form-group ">
                                <label class="col-lg-2 control-label " for="surname1"> Last name *</label>
                                <div class="col-lg-10">
                                    <input id="surname1" name="surname" type="text" class="required form-control">

                                </div>
                            </div>

                            <div class="form-group ">
                                <label class="col-lg-2 control-label " for="email1">Email *</label>
                                <div class="col-lg-10">
                                    <input id="email1" name="email" type="text" class="required email form-control">
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="tab-pane p-t-10 fade" id="finish-2">
                        <div class="row">
                            <div class="form-group clearfix">
                                <div class="col-lg-12">
                                    <div class="checkbox checkbox-primary">
                                        <input id="checkbox-h1" type="checkbox">
                                        <label for="checkbox-h1">
                                            I agree with the Terms and Conditions.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pager m-b-0 wizard">
                        <li class="previous disabled"><a  data-action="prev-tab"  href="#" class="btn btn-primary waves-effect waves-light">
                                <?php echo trans_with_global('Previous');?></a></li>
                        <li class="next "><a data-action="next-tab" href="#" class="btn btn-primary waves-effect waves-light">
                                <?php echo trans_with_global('Next');?></a></li></a></li>
                    </ul>


                </div>
            </div>

        </form>
    </div>
</div>
<script>
    // , 'lib/js/wizard'
    add_js([parse_view_name('System', 'Modules')]);
</script>