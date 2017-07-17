<div class="row">
    <div class="col-sm-12">
        <div class="card-box">


            <h4 class="header-title m-t-0 m-b-30"><?php echo trans('Modify Menu'); ?></h4>

            <div class="row">
                <form class="form-horizontal Menu-form" role="form">
                    <div class="col-lg-6">

                        <?php echo component_view('fields/enum/tree-edit', [
                            'id' => $row['id'],
                            'name' => 'parent_id',
                            'label' => 'parent',
                            'value' => $row['parent_id'],
                            'list' => & $menus,
                        ]); ?>

                        <?php echo component_view('fields/varchar/edit', ['name' => 'icon', 'label' => 'icon', 'value' => $row['icon']]); ?>


                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>

