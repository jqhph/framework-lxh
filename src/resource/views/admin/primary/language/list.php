<div class="col-md-2">
    <div class="card-box">
        <div class="card-box-header m-b-30">
            <span class="card-box-title"><?php echo trans('Catalog');?></span>
        </div>
        <?php echo render_view('component.tree.basic', ['class' => 'basic-language', 'list' => & $list]); ?>
    </div>
</div>

<div class="col-md-10">
    <div class="card-box">
        <form name="" class="Language-form" onsubmit="return false">
        <div class="pull-right">
            <button type="submit" data-action="save" class="btn btn-danger" style="display: none"><?php echo trans('Save'); ?></button>
            <button data-action="cancel" class="btn btn-default" style="display: none"><?php echo trans('Cancel'); ?></button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button  data-action="edit" class="btn btn-custom  "><?php echo trans_with_global('Edit'); ?></button>
            <button data-action="create-value"  class="btn btn-info "><?php echo trans('Create Value');?></button>
            <button data-action="create-options"  class="btn btn-success  "><?php echo trans('Create Options');?></button>
            <button data-action="create-category" class="btn btn-primary "><?php echo trans('Create Category');?></button>
            <button  data-action="create-file" class="btn btn-purple"><?php echo trans('Create File'); ?></button>
            <button  data-action="copy-file" class="btn btn-inverse"><?php echo trans('Copy File'); ?></button>
        </div>
        <h4 class="header-title m-t-0 m-b-30 package-title pull-left"><?php echo trans('Details'); ?></h4>
        <table class="table table-bordered m-0 language-table">
            <thead>
            <tr>
                <th class="col-md-1"><?php echo trans('category', 'fields'); ?></th>
                <th><?php echo trans('value', 'fields'); ?></th>
                <th class="remove-td" style="width: 5px;display: none"></th>
            </tr>
            </thead>

            <tbody>
            <tr><td class="col-md-1" colspan="2">&nbsp;</td></tr>
            </tbody>
        </table>
        </form>
    </div>
</div>

<script>
    require_css(['lib/plugins/jstree/style.css', 'lib/plugins/custombox/dist/custombox.min.css']);
    require_js([
        'lib/plugins/jstree/jstree.min',
        parse_view_name('Language', 'List'),
//        'lib/plugins/custombox/dist/custombox.min',
//        'lib/plugins/custombox/dist/legacy.min'
    ]);
</script>

<?php echo render_view('component.modal.basic');?>

<!-- js 模板 -->
<script type="text/html" id="row-tpl">
    @foreach {list} {k} {row}
    <tr>
        <td>
            <span class="text">{k}</span><input type="text" class="form-control" name="category[]" value="{k}" style="display: none"/>
            <input type="hidden" name="origin_category[]" value="{k}" />
        </td>
        <td>{#view 'tableTpl' row k#}</td>
        <td class="remove-td" style="width: 5px;display: none"><i data-action="remove-edit-row" class="fa fa-times" style="color:#ff5b5b;cursor:pointer"></i></td>
    </tr>
    @endforeach
</script>

<script type="text/html" id="tableTpl">
    <table class="table table-bordered m-0">
        @foreach {list} {k} {row}
        <tr>
            <td>
                <span class="text">{k}</span>
                <input type="text" class="form-control" name="{cate}_value_name[]" value="{k}" style="display: none"/>
                <input type="hidden" name="origin_{cate}_value_name[]" value="{k}" />
            </td>
            @if typeof {row} == 'string'
                <td><span class="text">{row}</span><input type="text" class="form-control" name="{cate}_value[]" value="{row}" style="display: none"/></td>
            @else
                <td>{#view 'tableTpl' row k#}</td>
            @endif
            <td class="remove-td" style="width: 5px;display: none"><i data-action="remove-edit-row" class="fa fa-times" style="color:#ff5b5b;cursor:pointer"></i></td>
        </tr>
        @endforeach
    </table>
</script>

<script type="text/html" id="createCategoryTpl">
    <hr>
    <?php echo render_view('component/fields/varchar/edit', ['label' => 'name', 'name' => 'cate_name'])?>
    <hr>
</script>

<script type="text/html" id="createFileTpl">
    <hr>
    <?php echo render_view('component/fields/varchar/edit', ['label' => 'language', 'name' => 'lang_name', 'value' => 'zh'])?>
    <?php echo render_view('component/fields/varchar/edit', ['label' => 'module', 'name' => 'module_name', 'value' => 'Admin'])?>
    <?php echo render_view('component/fields/varchar/edit', ['label' => 'filename', 'name' => 'filename', 'placeholder' => 'Avoid the suffix'])?>
    <hr>
</script>

<script type="text/html" id="createValueTpl">
    <div class="form-group clearfix">
        <label class="col-md-2 control-label"><?php echo trans('category', 'fields'); ?></label>

        <div class="col-md-10">
            <select name="category_name" class="form-control">
                @foreach {categories} {c}
                <option value="{c}">{c}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group clearfix">
        <label class="col-md-2 control-label"><?php echo trans('key-value', 'fields'); ?></label>
        <div class="col-md-4">
            <input value="" type="text" placeholder="key" name="key[]" class="form-control" >
        </div>

        <div class="col-md-5">
            <input value="" type="text" placeholder="value" name="value[]" class="form-control" >
        </div>
        <div class="col-md-1">
            <i data-action="add-key-value-row" class="fa fa-plus" style="color:#0eac5c;cursor:pointer"></i>
        </div>
    </div>

</script>

<script type="text/html" id="addKeyValueTpl">
    <div class="form-group clearfix">
        <label class="col-md-2 control-label"><?php echo trans('key-value', 'fields'); ?></label>
        <div class="col-md-4">
            <input value="" type="text" placeholder="key" name="key[]" class="form-control" >
        </div>

        <div class="col-md-5">
            <input value="" type="text" placeholder="value" name="value[]" class="form-control" >
        </div>
        <div class="col-md-1">
            <i data-action="remove-key-value-row" class="fa fa-times" style="color:#ff5b5b;cursor:pointer"></i>
        </div>
    </div>
</script>

<script type="text/html" id="createOptionsTpl">
    <div class="form-group clearfix">
        <label class="col-md-2 control-label"><?php echo trans('field name', 'fields'); ?></label>

        <div class="col-md-10">
            <input value="" type="text" placeholder="" name="field" class="form-control" >
        </div>
    </div>
    <div class="form-group clearfix">
        <label class="col-md-2 control-label"><?php echo trans('key-value', 'fields'); ?></label>
        <div class="col-md-4">
            <input value="" type="text" placeholder="key" name="key[]" class="form-control" >
        </div>

        <div class="col-md-5">
            <input value="" type="text" placeholder="value" name="value[]" class="form-control" >
        </div>
        <div class="col-md-1">
            <i data-action="add-key-value-row" class="fa fa-plus" style="color:#0eac5c;cursor:pointer"></i>
        </div>
    </div>

</script>

