<form <?php echo $attributes ?> pjax-container>
    <div class="box-body fields-group">
        <?php foreach($fields as $field): ?>
            <?php echo $field->render(); ?>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>
    <input type="hidden" name="_token" value="<?php ?>">
    <?php if ($footer) { ?></div>
    <div class="box-footer">
        <div class="col-sm-12"><?php echo $footer;?></div><div style="clear: both;height:5px;"></div>
    </div>
    <?php } ?>
</form>