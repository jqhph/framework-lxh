<div class="card" <?php echo $attributes ?>>
    <?php if ($title || $actions || $tools) {?>
    <div class="card-header"><h4 style="display:inline"><?php echo $title ?></h4>
        <span class="dropdown"><?php echo $actions?></span>
        <div class="pull-right">
            <div class="<?php echo $toolClass?>"><div class="dropdown-btn-group pull-right" style="margin-left:5px;"><?php echo $tools; ?></div></div>
        </div>
        <div class="divider"></div>
    </div>
    <?php } ?>
    <div class="card-body card-padding panel-collapse collapse in" id="<?php echo $id; ?>"><?php echo $content ?></div>
</div>