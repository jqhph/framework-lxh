<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php echo 'style="width:100%"';?>>
            <?php foreach ($options as &$opt) {
                $id = 'r'.Lxh\Helper\Util::randomString(6);
                ?>
            <div class="<?php echo $type?> <?php echo $type?>-<?php echo $color;?> <?php echo $inline?>">
                <input <?php
                if ($type == 'checkbox') {
                    if (in_array($opt['value'], $value)) echo 'checked';
                } else {
                    if ($value == $opt['value']) echo 'checked';
                }
                ?> id="<?php echo $id?>" <?php echo $attributes;?>  name="<?php echo $name;if ($type == 'checkbox') echo '[]';?>" value="<?php echo $opt['value']?>">
                <label for="<?php echo $id?>"><?php echo $opt['label']?></label>
            </div>
            <?php } ?>
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
