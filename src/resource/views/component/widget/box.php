<div <?php echo $attributes  ?>>
    <div class="portlet-heading portlet-default">
        <h3 class="portlet-title">
            <?php echo $title ?>
        </h3>
        <span>
            <?php foreach ($actions as &$action) {
               echo '&nbsp;' .$action;
            }?>
        </span>
        <div class="portlet-widgets">
            <div class="btn-toolbar"><div class="btn-group dropdown-btn-group pull-right">
            <?php foreach($tools as &$tool): ?>
                &nbsp;<?php echo $tool  ?>
            <?php endforeach; ?>
            </div></div>
        </div>
        <div style="clear: both"></div>
    </div>
    <div id="<?php echo $id; ?>" class="panel-collapse collapse in" aria-expanded="true" style="">
        <div class="portlet-body">
            <?php echo $content  ?>
        </div>
    </div>
</div>