<?php
$labelCategory = empty($labelCategory) ? 'labels' : $labelCategory;
$labelCol      = empty($labelCol) ? 2 : $labelCol;
$formCol       = empty($formCol) ? 8 : $formCol;
$hideLabe      = isset($hideLabe) ? $hideLabe : false;

?>

<div class="">
    <?php if (! $hideLabe) {?>
        <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($label, 'fields'); ?></label>
    <?php } ?>
    <div class="col-md-<?php echo $formCol;?>">
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