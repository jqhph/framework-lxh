<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text quick-edit-text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php echo 'style="width:100%"';?>>
            <textarea style="top:6px;" class="form-control" rows="<?php echo $rows?>" name="<?php echo $name?>" placeholder="<?php echo $placeholder?>"><?php echo $value?></textarea>
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
