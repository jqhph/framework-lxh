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

    <!-- 添加字段选项值 -->
    <div class="card-box field-options " style="display:none">

    </div>

</div> <!-- end col-->

<script type="text/html" id="fields-extra-options">
    <div class="field-options-{field}">
        <div class="card-box-header">
            <span class="card-box-title"><?php echo trans_with_global('Options');?> - {field}</span>
            <div class="pull-right"></div>
        </div>
        <div class="card-box-line m-b-30"></div>
        {#view 'fieldsExtraOptionsInput' field i #}
    </div>
</script>

<script type="text/html" id="fieldsExtraOptionsInput">
    <div class="row form-group">
        <label class="col-md-2 control-label" style="font-weight:normal;color:#333"><?php echo trans('option value', 'fields'); ?></label>
        <div class="col-md-2">
            <input value="" type="text" placeholder="value" name="{field}-value[]" class="form-control" >
        </div>
        <div class="col-md-2">
            <input value="" type="text" placeholder="Chinese" name="{field}-Chinese[]" class="form-control" >
        </div>
        <div class="col-md-2">
            <input value="" type="text" placeholder="English" name="{field}-English[]" class="form-control" >
        </div>
        <div class="col-md-1">{btn}</div>
    </div>
</script>

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
