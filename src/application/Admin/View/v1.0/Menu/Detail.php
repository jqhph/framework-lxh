<!-- Top Bar Start -->
<?php

if (empty($row)) {
    $row = [];
}
use Lxh\Kernel\AdminUrlCreator;

echo fetch_view('top-bar', 'Public', ['nav' => $currentTitle]); ?>
<!-- Top Bar End -->


<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30"><?php echo trans_with_global('Primary');?></h4>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">
                        <?php if (! empty($row['id'])) { ?>
                        <input type="hidden" name="id" value="<?php echo $row['id'];?>" />
                        <?php } ?>

                        <?php echo component_view('fields/enum/tree-edit', [
                            'id' => get_value($row, 'id'),
                            'name' => 'parent_id',
                            'label' => 'parent',
                            'value' => get_value($row, 'parent_id'),
                            'list' => & $menus,
                        ]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'icon', 'label' => 'icon', 'value' => get_value($row, 'icon')]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'name', 'label' => 'name', 'value' => get_value($row, 'name')]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'controller', 'label' => 'controller', 'value' => get_value($row, 'controller')]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'action', 'label' => 'action', 'value' => get_value($row, 'action')]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'priority', 'label' => 'priority', 'value' => get_value($row, 'priority', 0)]); ?>

                        <?php echo component_view('fields/bool/edit', ['name' => 'show', 'label' => 'show', 'value' => get_value($row, 'show', 1)]); ?>

                        <?php echo component_view('detail-button');?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<script>
    add_js(parse_view_name('Menu', 'detail'));
</script>
