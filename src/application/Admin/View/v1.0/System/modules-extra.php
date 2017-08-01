<div class="col-sm-12">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="m-b-30">
<!--                        <button data-action="addToTable" class="btn btn-inverse waves-effect waves-light">--><?php //echo trans_with_global('Add');?><!-- <i class="fa fa-plus"></i></button>-->
                    </div>
                </div>
            </div>

            <div class="editable-responsive fields-extra">
                <table class="table m-0">
                    <thead>
                    <tr>
                        <th><?php echo trans('Fields name');?></th>
                        <th><?php echo trans('Rank');?></th>
                        <th><?php echo trans('Group');?></th>
                        <th><?php echo trans('Sorting items');?></th>
                        <th><?php echo trans('Search terms');?></th>
                        <th><?php echo trans('List Items');?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- end: panel body -->

    </div> <!-- end panel -->
</div> <!-- end col-->

<!-- blade模板文件内容 -->
<script type="text/html" id="fields-extra-edit-rows">
    <tr data-name="{fieldName}">
        <td scope="row"><input name="fieldsName[]" class="form-control" readonly value="{fieldName}"/></td>
        <td><input name="rank[]" class="form-control" value="0"/></td>
        <td><?php echo component_view('fields/enum/edit',
                ['name' => 'group[]', 'hideLabe' => true, 'formCol' => '', 'formGroup' => '', 'opts' => & $groups]); ?></td>
        <td><div class="checkbox checkbox-danger"><input value="1" name="sorting[]" type="checkbox" checked /><label></label></div></td>
        <td><div class="checkbox checkbox-danger"><input value="1" name="search[]" type="checkbox" checked /><label></label></div></td>
        <td><div class="checkbox checkbox-danger"><input value="1" name="list[]" type="checkbox" checked /><label></label></div></td>
        <td></td>
    </tr>
</script>
