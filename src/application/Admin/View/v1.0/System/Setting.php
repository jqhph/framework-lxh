<?php

?>

<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="card-box-header">
                <span class="card-box-title"><?php echo trans('Config');?></span>
                <div class="pull-right">
                    <a data-action="clear-client-cache" class="btn btn-purple "><?php echo trans('Clear client cache')?></a>
                    <a data-action="clear-js-css-cache" class="btn btn-custom "><?php echo trans('Clear all client cache')?></a>
                </div>
            </div>
            <div class="card-box-line m-b-30"></div>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">

                        <?php echo component_view('fields/enum/edit', [
                            'name' => 'language', 'label' => 'Language', 'value' => config('language'), 'list' => & $languageList, 'labelCol' => 4]); ?>

                        <?php echo component_view('fields/varchar/edit', [
                            'name' => 'lang-package-expire', 'label' => 'Language package expire after',
                            'value' => config('replica-client-config.lang-package-expire') / 1000, 'labelCol' => 4]); ?>

                        <?php echo component_view('fields/varchar/edit', [
                            'name' => 'cache-expire', 'label' => 'Cache expire after',
                            'value' => config('replica-client-config.cache-expire') / 1000, 'labelCol' => 4]); ?>

                        <?php echo component_view('fields/bool/edit', [
                            'name' => 'use-cache', 'label' => 'Use cache', 'value' => config('replica-client-config.use-cache'), 'labelCol' => 4]); ?>


                        <?php echo component_view('detail-button');?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<script>
    add_js([parse_view_name('System', 'setting')]);
</script>