<div <?php echo  $attributes ?>>
    <ul class="nav nav-tabs">
        <?php foreach($tabs as $id => &$tab) { ?>
        <li <?php echo  $id == 0 ? 'class=active' : '' ?>><a href="#tab_<?php echo  $tab['id'] ?>" data-toggle="tab"><?php echo  $tab['title'] ?></a></li>
        <?php } ?>
        <?php if (!empty($dropDown)) {?>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Dropdown <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php foreach($dropDown as &$link) { ?>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo  $link['href'] ?>"><?php echo  $link['name'] ?></a></li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
        <li class="pull-right header"><?php echo  $title ?></li>
    </ul>
    <div class="tab-content">
        <?php foreach($tabs as $id => &$tab) { ?>
        <div class="tab-pane <?php echo  $id == 0 ? 'active' : '' ?>" id="tab_<?php echo  $tab['id'] ?>">
            <?php
            if ($tab['content'] instanceof \Lxh\Contracts\Support\Renderable) {
                echo $tab['content']->render();
            } else {
                echo  $tab['content'];
            }
             ?>
        </div>
        <?php } ?>

    </div>
</div>
