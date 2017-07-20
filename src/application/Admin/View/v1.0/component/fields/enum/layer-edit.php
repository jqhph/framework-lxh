<?php
$labelCategory = empty($labelCategory) ? 'labels' : $labelCategory;

?>

<div class="">
    <?php if (! empty($label)) {?>
        <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <?php } ?>
    <div class="<?php echo empty($formWidth) ? '' : $formWidth;//col-md-8?>">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($list as & $row) { ?>
                <optgroup label="<?php echo trans($row['label']); ?>">
                    <?php foreach ($row['list'] as & $v) {
                        if ($v['id'] == $id && empty($v['required'])) continue;
                        ?>
                    <option <?php if ($value == $v['id']) echo 'selected';?> value="<?php echo $v['id'];?>">
                        <?php echo trans($v['name'], $labelCategory);?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </div>
</div>