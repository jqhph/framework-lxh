<div class="form-group">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>">
        <select class="form-control <?php echo $class ?>" style="width:100%;" name="<?php echo $name ?>[]" multiple="multiple" data-placeholder="<?php echo  $placeholder  ?>" <?php echo $attributes ?> >
            <?php foreach($options as &$option): ?>
            <option value="<?php echo  $option['value'] ?>" <?php echo in_array($option['value'], $value) ?'selected':''  ?>><?php echo  $option['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="<?php echo $name ?>[]" />
        <?php if ($help) {
            echo view('admin::form.help-block')->render();
        }?>
    </div>
</div>
