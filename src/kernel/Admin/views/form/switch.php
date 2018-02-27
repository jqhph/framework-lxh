<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>" style="margin-top:10px;">
        <div class="text" style="display:inline;"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>&nbsp;&nbsp;&nbsp;
        <input name="<?php echo $name ?>" <?php echo $value?'checked':''  ?> value="<?php echo $value?>" type="checkbox" data-plugin="switchery" <?php echo $attributes ?>/>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>