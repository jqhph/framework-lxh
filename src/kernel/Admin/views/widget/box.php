<div <?php echo $attributes  ?>>
    <div class="portlet-heading portlet-default">
        <h3 class="portlet-title"><?php echo $title ?></h3>
        <span class="dropdown"><?php echo $actions?></span>
        <div class="portlet-widgets">
            <div class="<?php echo $toolClass?>"><div class="btn-group dropdown-btn-group pull-right">
            <?php echo $tools; ?>
            </div></div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="<?php echo $id; ?>" class="panel-collapse collapse in">
        <div class="portlet-body"><?php echo $content  ?></div>
    </div>
</div>