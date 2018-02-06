<form <?php echo $attributes ?> pjax-container>
    <div class="box-body fields-group">
        <?php foreach($fields as $field): ?>
            <?php echo $field->render(); ?>
        <?php endforeach; ?>
        <div style="clear:both;"></div>
    </div>
    <?php if ($footer) { ?>
    <div class="box-footer" style="padding:10px 0 0;"><div class="col-sm-12"><?php echo $footer;?></div><div style="clear:both;<?php if (!$useModal) {echo 'height:5px;';}?>"></div></div>
    <?php } ?>
</form>