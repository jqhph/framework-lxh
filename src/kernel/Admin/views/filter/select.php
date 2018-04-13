<div class="form-group input-group-sm filter-input col-sm-<?php echo $width['field'] ?> input-group " style="margin-left:10px;" >
    <span class="input-group-addon"><b><?php echo $label ?></b></span>
    <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name ?>" <?php echo $attributes ?> >
        <option></option>
        <?php foreach($options as &$option): ?>
            <option value="<?php echo $option['value'] ?>" <?php
            if ($value !== null) {
                if ($option['value'] === 0 || $option['value'] === '0') {
                    echo  ((int)$option['value']) == (int)$value ?'selected':'';
                } else {
                    echo  $option['value'] == $value ?'selected':'';
                }
            }

            ?>><?php echo $option['label'] ?></option>
        <?php endforeach; ?>
    </select>
</div>