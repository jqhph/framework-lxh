<div class="form-group filter-input col-sm-<?php echo $width['field'] ?> input-group " style="margin-left:10px;" input-group-sm>
    <span class="input-group-addon"><b><?php echo $label ?></b></span>
    <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name ?>" <?php echo $attributes ?> >
        <option></option>
        <?php foreach($options as &$option): ?>
            <option value="<?php echo $option['value'] ?>" <?php echo  $option['value'] == $value ?'selected':''  ?>><?php echo $option['label'] ?></option>
        <?php endforeach; ?>
    </select>
</div>