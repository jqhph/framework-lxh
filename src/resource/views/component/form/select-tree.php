<div class="form-group <?php //echo !$errors->has($errorKey) ?: 'has-error' ?>">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>">
      
        <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name?>" <?php echo $attributes ?> >
            <?php if ($defaultOption) {?>
                <option value="<?php echo $defaultOption['value'] ?>" ><?php echo $defaultOption['label']?></option>
            <?php } ?>
            <?php foreach($options as $select => &$option): ?>
            <option value="<?php echo $option['id'] ?>" <?php echo $option['id'] == $value ?'selected':''  ?>><?php echo $option['name']?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => $help])->render();
        }?>
    </div>
</div>
