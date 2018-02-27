<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name ?>" <?php echo $attributes ?> >
            <?php if ($defaultOption) {?>
                <option value="<?php echo $defaultOption['value'] ?>" ><?php echo $defaultOption['label']?></option>
            <?php } ?>
            <?php foreach($options as &$option): ?>
            <option value="<?php echo $option['value'] ?>" <?php
            if (is_numeric($value)) $value = (int) $value;
            echo  $option['value'] === $value ?'selected':''  ?>><?php echo $option['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
