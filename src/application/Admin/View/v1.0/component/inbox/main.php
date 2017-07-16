<div class="<?php echo $args->topClass;?>">
    <div class="card-box">
        <div class="dropdown pull-right">
            <?php if ($args->menus) {
                // 下拉菜单
                ?>
                <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                    <i class="zmdi zmdi-more-vert"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($args->menus as & $m) { ?>
                        <li><a href="#"><?php echo trans($m); ?></a></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <h4 class="header-title m-t-0 m-b-30"><?php echo trans($args->title);?></h4>

        <div class="inbox-widget nicescroll" style="height: 315px; overflow: hidden; outline: none;" tabindex="5000">
            <?php foreach ($args->contents as & $c) { ?>
                <a href="<?php echo $c['url'];?>">
                    <div class="inbox-item">
                        <?php if ($c['pic']) { ?>
                            <div class="inbox-item-img"><img src="<?php echo $c['pic'];?>" class="img-circle" alt=""></div>
                        <?php } ?>

                        <p class="inbox-item-author"><?php echo trans($c['author']);?></p>
                        <p class="inbox-item-text"><?php echo trans($c['text']);?></p>
                        <p class="inbox-item-date"><?php echo trans($c['date']);?></p>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</div>