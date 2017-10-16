<?php
$menu = make('acl-menu');
$admin = admin();
$name = $admin->first_name . $admin->last_name;
?>


<?php foreach ($menu->get() as & $m): ?>
<div class="portlet <?php echo empty($m['subs']) ? 'waves-effect waves-success' : '';?>" style="width:100%;">
    <div class="portlet-heading bg-" style="padding:6px 18px;">
        <h3 data-action="switch-menu" data-id="<?php echo $m['id'];?>" data-name="<?php echo $m['name'];?>" data-url="<?php echo empty($m['subs']) ? $m['url'] : '';?>" class="portlet-title" style="color: #333;font-size:13px;cursor:pointer">
            <i class="<?php echo $m['icon'];?>"></i> <?php echo $m['name'];?>
        </h3>
        <?php if (! empty($m['subs'])): ?>
        <div class="portlet-widgets">
            <a data-toggle="collapse" data-parent="" href="#menu-<?php echo $m['id'];?>"><i class="zmdi zmdi-minus"></i></a>
        </div>
        <?php endif;?>
        <div class="clearfix"></div>
    </div>
    <?php if (! empty($m['subs'])): ?>
    <div style="border-bottom:1px solid #eee"></div>
    <div id="menu-<?php echo $m['id'];?>" class="panel-collapse collapse in">
        <div class="portlet-body" style="padding:2px 15px 15px 15px">

            <?php foreach ($m['subs'] as & $sub): ?>
            <div class="menu-btn" >
                <span data-action="switch-menu" data-id="<?php echo $sub['id'];?>" data-name="<?php echo $sub['name'];?>" data-url="<?php echo $sub['url'];?>" class="btn btn-trans btn-default btn-menu  waves-effect waves-success">
                    <?php echo $sub['name'];?></span>
            </div>
            <?php endforeach; ?>
            <div style="clear: both"></div>

        </div>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>