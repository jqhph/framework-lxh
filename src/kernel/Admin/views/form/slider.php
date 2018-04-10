<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php echo 'style="width:100%"';?>>
            <input type="text" class="<?php echo $class?>" name="<?php echo $name?>" data-from="<?php echo $value ?>" <?php echo $attributes ?> />
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
