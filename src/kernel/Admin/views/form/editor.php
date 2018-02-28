<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label; ?></div>
        <div class="input-group" <?php if (!$append) {echo 'style="width:100%"';}?>><div <?php echo $attributes?> ></div></div>
    </div>
</div>