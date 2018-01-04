<div <?php echo $attributes;?> tabindex="-1" role="dialog" aria-labelledby aria-hidden="true" >
    <div class="modal-dialog" style="<?php echo $style?>">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?php echo $title;?></h4>
            </div>
            <div class="modal-body"><?php echo $body;?></div>
            <?php if ($footer) {?>
            <div class="modal-footer"><?php echo $footer;?></div>
            <?php } ?>
        </div>
    </div>
</div>