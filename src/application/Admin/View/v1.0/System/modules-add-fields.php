<div class="col-sm-12">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="m-b-30">
                        <button data-action="addToTable" class="btn btn-inverse waves-effect waves-light"><?php echo trans_with_global('Add');?> <i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <div class="editable-responsive edit-fields">
                <table class="table m-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo trans('Fields name');?></th>
                        <th><?php echo trans('Fields english name');?></th>
                        <th><?php echo trans('Fields chinese name');?></th>
                        <th><?php echo trans('Field type');?></th>
                        <th><?php echo trans('Default values');?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>id</td>
                        <td>ID</td>
                        <td>ID</td>
                        <td></td>
                        <td></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- end: panel body -->

    </div> <!-- end panel -->
</div> <!-- end col-->

<!-- blade模板文件内容 -->
<script type="text/html" id="add-fields-edit-rows">
    <tr>
        <th scope="row">{no}</th>
        <td><input name="field_name[]" class="form-control" value=""/></td>
        <td><input name="field_en_name[]" class="form-control" value=""/></td>
        <td><input name="field_zh_name[]" class="form-control" value=""/></td>
        <td><?php echo component_view('fields/enum/layer-edit', [
                'id' => '@',
                'name' => 'field_type[]',
                'label' => '',
                'value' => '',
                'list' => & $fields,
            ]); ?></td>
        <td><input name="field_default[]" class="form-control" value=""/></td>
        <td><i data-action="remove-edit-row" class="fa fa-times" style="color:#ff5b5b;cursor:pointer"></i></td>
    </tr>
</script>
