<div class="card-box" <?php echo $attributes;?> >

    <?php if ($actions) {?>
    <div class="dropdown pull-right">
        <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="zmdi zmdi-more-vert"></i>
        </a>
        <ul class="dropdown-menu" role="menu">
        <?php foreach ($actions as &$rows) {?>
            <?php foreach ($rows as &$row) {?>
                <li class="<?php echo getvalue($row, 'class');?>"><a href="<?php echo getvalue($row, 'url', '#');?>"><?php echo $row['value']; ?></a></li>
            <?php } ?>
            <?php if (count($actions) > 1) echo ' <li class="divider"></li>';?>
        <?php } ?>
        </ul>
    </div>
    <?php }?>

    <h4 class="header-title m-t-0 m-b-30">
        <span onclick="open_tab('infobox-<?php echo $name?>', '<?php echo $link?>', '<?php echo $name?>')">
            <i class="<?php echo $icon;?>"></i> <?php echo $name?>
        </span>
    </h4>


    <div class="widget-box-2">
        <div class="widget-detail-2">
            <?php if ($badge) { ?>
            <span class="badge badge-<?php echo $color?> pull-left m-t-20"><?php echo $badge?> <i class="zmdi zmdi-trending-up"></i> </span>
            <?php } ?>
            <h2 class="m-b-0"> <?php echo $info?> </h2>
            <p class="text-muted m-b-25"><?php echo $label?></p>
        </div>

        <?php if (!empty($progress)) {?>
        <div class="progress progress-bar-success-alt progress-sm m-b-0">
            <div class="progress-bar progress-bar-<?php echo getvalue($progress, 'color', 'success')?>"
                 role="progressbar" aria-valuenow="<?php echo getvalue($progress, 'value');?>"
                 aria-valuemin="0" aria-valuemax="100" style="width:<?php echo getvalue($progress, 'value');?>%;">
                <span class="sr-only"><?php echo getvalue($progress, 'value');?>% Complete</span>
            </div>
        </div>
        <?php }?>
    </div>
</div>