<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>" style="margin-top:10px;">
        <div class="text" style="margin:8px 0 -5px"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>&nbsp;&nbsp;&nbsp;
        <div class="input-group" <?php if (!$append) {echo 'style="width:100%"';}?>>
            <input name="<?php echo $name ?>" <?php echo $value?'checked':''  ?> value="<?php echo $value ?: 1?>" type="checkbox" data-plugin="switchery" <?php echo $attributes ?>/>
        </div>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>