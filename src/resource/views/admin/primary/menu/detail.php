<?php

if (empty($row)) {
    $row = [];
}
use Lxh\Admin\Kernel\Url;
?>



<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="card-box-header">
                <span class="card-box-title"><?php echo trans_with_global('Primary');?></span>
                <div class="pull-right"></div>
            </div>
            <div class="card-box-line m-b-30"></div>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">
                        <?php if (! empty($row['id'])) { ?>
                            <input type="hidden" name="id" value="<?php echo $row['id'];?>" />
                        <?php } ?>

                        <?php echo render_view('component/fields/enum/tree-edit', [
                            'id' => get_value($row, 'id'),
                            'name' => 'parent_id',
                            'label' => 'parent',
                            'value' => get_value($row, 'parent_id'),
                            'list' => & $menus,
                        ]); ?>

                        <?php echo render_view('component.fields.varchar.edit', [
                            'name' => 'icon', 'label' => 'icon', 'value' => get_value($row, 'icon'),
                            'help' => '<a target="_blank" href="' . Url::makeAction('font-awesome', 'public-entrance')
                                . '">' . trans_with_global('fontawesome icon CSS') . '</a>'
                        ]); ?>

                        <?php echo render_view('component.fields.varchar.edit', ['name' => 'name', 'label' => 'name', 'value' => get_value($row, 'name')]); ?>

                        <?php echo render_view('component.fields.varchar.edit', ['name' => 'controller', 'label' => 'controller', 'value' => get_value($row, 'controller')]); ?>

                        <?php echo render_view('component.fields.varchar.edit', ['name' => 'action', 'label' => 'action', 'value' => get_value($row, 'action')]); ?>

                        <?php echo render_view('component.fields.varchar.edit', ['name' => 'priority', 'label' => 'priority', 'value' => get_value($row, 'priority', 0)]); ?>

                        <?php echo render_view('component.fields.bool.edit', ['name' => 'show', 'label' => 'show', 'value' => get_value($row, 'show', 1)]); ?>

                        <?php echo render_view('component.detail-button', ['id' => get_value($row, 'id')]);?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<script>
    add_js(parse_view_name('Menu', 'detail'));
</script>
