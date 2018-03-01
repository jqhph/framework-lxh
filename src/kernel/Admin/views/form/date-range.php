<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php if (!$append) {echo 'style="width:100%"';}?>>
            <div class="col-lg-6" style="max-width:250px;padding:0">
                <input value="<?php echo isset($value['start']) ? $value['start'] : $defaultValue['start'];?>" type="text" class="form-control <?php echo $name;?>-start" name="<?php echo $name;?>-start" placeholder="<?php echo trans_with_global('start')?>">
            </div>
            <div class="col-lg-6 input-group">
                <span class="input-group-addon btn-custom btn-trans b-0 text-white">to</span>
                <input style="padding-left:8px;max-width:250px;" value="<?php echo isset($value['end']) ? $value['end'] : $defaultValue['end'];?>" type="text" class="form-control <?php echo $name;?>-end" name="<?php echo $name;?>-end"  placeholder="<?php echo trans_with_global('end')?>">
            </div>
            <?php if ($append) {?>
                <span class="input-group-addon clearfix"><?php echo $append ?></span>
            <?php } ?>
            <?php if ($options) {?>
                <ul class="dropdown-menu col-sm-12">
                    <?php foreach ((array)$options as &$v) {?>
                        <li><a><?php echo $v;?></a></li>
                    <?php }?>
                </ul>
            <?php }?>
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>