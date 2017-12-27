<div class="form-group <?php // echo !$errors->has($column) ?: 'has-error' ?>">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>" id="<?php echo $id ?>">
<!--        @include('admin::form.error')-->
        <?php foreach($options as $option => $label): ?>
        <?php if(!$inline) {?><div class="checkbox"> <?php }?>
            <label <?php if(!$inline) {?>class="checkbox-inline" <?php }?>>
                <input type="checkbox" name="<?php echo $name ?>[]" value="<?php echo $option ?>" class="<?php echo $class ?>" <?php echo in_array($option, $value)?'checked':''  ?> <?php echo $attributes ?> />&nbsp;<?php echo $label ?>&nbsp;&nbsp;
            </label>
            <?php if(!$inline) {?></div> <?php }?>
        <?php endforeach ?>

        <input type="hidden" name="<?php echo $name ?>[]">
        <?php if ($help) {
            echo view('admin::form.help-block')->render();
        }?>
    </div>
</div>
