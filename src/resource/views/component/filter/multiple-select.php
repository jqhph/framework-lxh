<div class="form-group filter-input col-sm-<?php echo $width['field'] ?> input-group input-group-sm" style="margin-left:10px;">
    <span class="input-group-addon"><b><?php echo $label ?></b></span>
    <select class="form-control <?php echo $class ?>" style="width:100%;" name="<?php echo $name ?>[]" multiple="multiple" data-placeholder="<?php echo  $placeholder  ?>" <?php echo $attributes ?> >
        <?php foreach($options as &$option): ?>
            <option value="<?php echo  $option['value'] ?>"  <?php echo in_array($option['value'], $value) ?'selected':''  ?>><?php echo  $option['label'] ?></option>
        <?php endforeach; ?>
    </select>
    <?php if ($help) {
        echo view('admin::form.help-block', ['help' => &$help])->render();
    }?>
</div>