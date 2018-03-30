<div class="form-group input-group-sm filter-input col-sm-<?php echo $width['field'] ?> input-group " style="margin-left:10px;" >
    <span class="input-group-addon"><b><?php echo $label ?></b></span>
    <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name ?>" <?php echo $attributes ?> >
        <?php if ($defaultOption) {?>
            <option value="<?php echo $defaultOption['value'] ?>" ><?php echo $defaultOption['label']?></option>
        <?php } ?>
        <?php foreach($options as &$option): ?>
            <option value="<?php echo $option['id'] ?>" <?php echo $option['id'] == $value ?'selected':''  ?>><?php echo $option['name']?></option>
        <?php endforeach; ?>
    </select>

    <?php if ($help) {
        echo view('admin::form.help-block', ['help' => &$help])->render();
    }?>
</div>