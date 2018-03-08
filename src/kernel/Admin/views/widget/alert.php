<div <?php echo  $attributes ?> >
    <?php if ($closeable) {?><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?php }?>
    <?php if ($title) {?><h4><?php if ($icon) {?><i class="icon fa fa-<?php echo $icon ?>"></i><?php }?> <?php echo $title ?></h4><?php }?>
    <?php echo $content ?>
</div>