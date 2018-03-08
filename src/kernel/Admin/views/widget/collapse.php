<div id="<?php echo $id?>" role="tablist" aria-multiselectable="true" <?php echo $attributes?>>
    <?php foreach ($items as &$item) {
        $id1 = 'c'.Lxh\Helper\Util::randomString(6);
        $id2 = 'ac'.Lxh\Helper\Util::randomString(6);
        ?>
    <div class="panel panel-default bx-shadow-none">
        <div class="panel-heading" role="tab" id="<?php echo $id1?>">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="<?php echo $id?>" href="#<?php echo $id2?>" aria-expanded="false" aria-controls="<?php echo $id2?>" >
                    <?php echo $item['title'];?>
                </a>
            </h4>
        </div>
        <div id="<?php echo $id2?>" class="panel-collapse collapse <?php echo !empty($item['show']) ? 'in' : ''?>" role="tabpanel" aria-labelledby="<?php echo $id1?>" aria-expanded="<?php echo !empty($item['show']) ? 'true' : 'false'?>">
            <div class="panel-body"><?php echo $item['content'];?></div>
        </div>
    </div>
    <?php } ?>
</div>