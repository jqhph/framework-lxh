<div class="filter-input col-sm-<?php echo $width['field'] ?>">
    <div class="input-group input-group-sm" >
        <span class="input-group-addon"><b><?php echo $label ?></b></span>
        <input <?php echo $attributes ?>/>
        <?php if ($options) {?>
        <ul class="dropdown-menu col-sm-12">
            <?php foreach ((array)$options as &$v) {?>
           <li><a><?php echo $v;?></a></li>
            <?php }?>
        </ul>
        <?php }?>
    </div>
</div>