<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>" id="<?php echo $id ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <?php foreach($options as $option => &$label): ?>
        <?php if(!$inline) {?><div class="checkbox"> <?php }?>
            <label <?php if(!$inline) {?>class="checkbox-inline" <?php }?>>
                <input type="checkbox" name="<?php echo $name ?>[]" value="<?php echo $option ?>" class="<?php echo $class ?>" <?php echo in_array($option, $value)?'checked':''  ?> <?php echo $attributes ?> />&nbsp;<?php echo $label ?>&nbsp;&nbsp;
            </label>
            <?php if(!$inline) {?></div> <?php }?>
        <?php endforeach ?>

        <input type="hidden" name="<?php echo $name ?>[]">
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
