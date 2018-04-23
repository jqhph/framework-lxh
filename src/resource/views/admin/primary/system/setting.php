<?php

?>

<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="card-box-header">
                <span class="card-box-title"><?php echo trans('Config');?></span>
                <div class="pull-right">
<!--                    <a data-action="clear-client-cache" class="btn btn-purple ">--><?php //echo trans('Clear client cache')?><!--</a>-->
                    <a data-action="clear-js-css-cache" class="btn btn-purple "><?php echo trans('Clear cache')?></a>
                </div>
            </div>
            <div class="card-box-line m-b-30"></div>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">

                        <?php echo render_view('component.fields/enum/edit', [
                            'name' => 'language', 'value' => config('locale'), 'opts' => & $languageList, 'labelCol' => 4]); ?>

                        <?php echo render_view('component.fields/varchar/edit', [
                            'name' => 'lang-package-expire', 'label' => 'Language package expire after',
                            'value' => config('replica-client-config.lang-package-expire') / 1000, 'labelCol' => 4]); ?>

                        <?php echo render_view('component.fields/varchar/edit', [
                            'name' => 'cache-expire', 'label' => 'Cache expire after',
                            'value' => config('replica-client-config.cache-expire') / 1000, 'labelCol' => 4]); ?>

                        <?php echo render_view('component.fields/bool/edit', [
                            'name' => 'use-cache', 'label' => 'Use cache', 'value' => config('replica-client-config.use-cache'), 'labelCol' => 4]); ?>


                        <?php echo render_view('component.detail-button');?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<script>
    require_js([parse_view_name('System', 'setting')]);
</script>