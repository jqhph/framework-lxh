<!-- Top Bar Start -->
<?php

use Lxh\Kernel\AdminUrlCreator;

$currentTitle = trans('Modify menu');

echo fetch_view('top-bar', 'Public', ['nav' => $currentTitle])?>
<!-- Top Bar End -->


<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30"><?php echo trans_with_global('Primary');?></h4>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">
                        <input type="hidden" name="id" value="<?php echo $row['id'];?>" />

                        <?php echo component_view('fields/enum/tree-edit', [
                            'id' => $row['id'],
                            'name' => 'parent_id',
                            'label' => 'parent',
                            'value' => $row['parent_id'],
                            'list' => & $menus,
                        ]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'icon', 'label' => 'icon', 'value' => $row['icon']]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'name', 'label' => 'name', 'value' => $row['name']]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'controller', 'label' => 'controller', 'value' => $row['controller']]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'action', 'label' => 'action', 'value' => $row['action']]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'priority', 'label' => 'priority', 'value' => $row['priority']]); ?>

                        <?php echo component_view('fields/bool/edit', ['name' => 'show', 'label' => 'show', 'value' => $row['show']]); ?>

                        <?php echo component_view('detail-button', ['back' => AdminUrlCreator::makeAction('Menu', 'Index')]);?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<script>
    add_js(parse_view_name('Menu', 'detail'));
</script>
